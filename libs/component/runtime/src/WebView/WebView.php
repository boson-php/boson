<?php

declare(strict_types=1);

namespace Boson\WebView;

use Boson\Application;
use Boson\Component\Http\Request;
use Boson\Component\Saucer\SaucerInterface;
use Boson\Contracts\EventListener\EventListenerInterface;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Contracts\Uri\UriInterface;
use Boson\Dispatcher\DelegateEventListener;
use Boson\Dispatcher\EventListener;
use Boson\Dispatcher\EventListenerProvider;
use Boson\Extension\Exception\ExtensionNotFoundException;
use Boson\Extension\Registry;
use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Api\Bindings\BindingsApi;
use Boson\WebView\Api\Bindings\BindingsApiInterface;
use Boson\WebView\Api\Bindings\Exception\FunctionAlreadyDefinedException;
use Boson\WebView\Api\Data\DataRetriever;
use Boson\WebView\Api\Data\DataRetrieverInterface;
use Boson\WebView\Api\Scripts\ScriptsApi;
use Boson\WebView\Api\Scripts\ScriptsApiInterface;
use Boson\WebView\Api\WebComponents\Exception\ComponentAlreadyDefinedException;
use Boson\WebView\Api\WebComponents\Exception\WebComponentsApiException;
use Boson\WebView\Api\WebComponents\WebComponentsApiInterface;
use Boson\WebView\Exception\WindowDereferenceException;
use Boson\Window\Window;
use Boson\Window\WindowId;
use Internal\Destroy\Destroyable;
use JetBrains\PhpStorm\Language;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @template-implements IdentifiableInterface<WebViewId>
 */
#[\AllowDynamicProperties]
final class WebView implements
    IdentifiableInterface,
    EventListenerInterface,
    ContainerInterface,
    Destroyable
{
    use EventListenerProvider;

    /**
     * Gets a reference to the parent application to which the
     * specified webview instance belongs.
     */
    public Application $app {
        get => $this->window->app;
    }

    /**
     * Gets a reference to the parent window to which the
     * specified webview instance belongs.
     */
    public Window $window {
        /**
         * @throws WindowDereferenceException in case of parent window has been removed
         */
        get => $this->reference->get()
            ?? throw WindowDereferenceException::becauseNoParentWindow();
    }

    /**
     * Contains webview URI instance.
     *
     * @api
     */
    public UriInterface $url {
        /**
         * Gets current webview URI instance.
         *
         * ```
         * echo $webview->url; // http://example.com
         * ```
         */
        get {
            $result = $this->saucer->saucer_webview_url($this->id->ptr);

            try {
                return Request::castUrl(\FFI::string($result));
            } finally {
                \FFI::free($result);
            }
        }
        /**
         * Updates URI of the webview.
         *
         * This can also be considered as navigation to a specific web page.
         *
         * ```
         * $webview->url = 'http://example.com';
         * ```
         */
        set(\Stringable|string $value) {
            $this->saucer->saucer_webview_set_url_str($this->id->ptr, (string) $value);
        }
    }

    /**
     * Load HTML content into the WebView.
     *
     * @api
     */
    public string $html {
        set(#[Language('HTML')] \Stringable|string $html) {
            $this->saucer->saucer_webview_set_html($this->id->ptr, $html);
        }
    }

    /**
     * Gets webview status.
     *
     * @api
     */
    public private(set) WebViewState $state = WebViewState::Loading;

    /**
     * WebView-aware event listener & dispatcher.
     */
    private readonly EventListener $listener;

    /**
     * List of webview extensions.
     *
     * @var Registry<WebView>
     */
    private readonly Registry $extensions;

    /**
     * @var \WeakReference<Window>
     */
    private readonly \WeakReference $reference;

    /**
     * @internal Please do not use the constructor directly. There is a
     *           corresponding {@see WindowFactoryInterface::create()} method
     *           for creating new windows with single webview child instance,
     *           which ensures safe creation.
     *           ```
     *           $app = new Application();
     *
     *           // Should be used instead of calling the constructor
     *           $window = $app->windows->create();
     *
     *           // Access to webview child instance
     *           $webview = $window->webview;
     *           ```
     */
    public function __construct(
        /**
         * Contains shared WebView API library.
         */
        private readonly SaucerInterface $saucer,
        /**
         * The webview identifier.
         *
         * In terms of implementation, it is equals to
         * the {@see WindowId} Window's ID.
         *
         * @api
         */
        public readonly WebViewId $id,
        private readonly Window $parent,
        /**
         * Gets information DTO about the webview with which it was created.
         */
        public readonly WebViewCreateInfo $info,
        EventDispatcherInterface $dispatcher,
    ) {
        // Parent reference
        $this->reference = \WeakReference::create($parent);

        // Initialization WebView's fields and properties
        $this->listener = self::createEventListener($dispatcher);

        // Initialization of WebView's API
        $this->extensions = new Registry($this->listener, $info->extensions);
        foreach ($this->extensions->boot($this) as $property => $extension) {
            // Direct access to dynamic property is 5+ times
            // faster than magic `__get` call.
            $this->__set($property, $extension);
        }
    }

    /**
     * @template TArgService of object
     *
     * @param class-string<TArgService>|string $id
     *
     * @return ($id is class-string<TArgService> ? TArgService : object)
     * @throws ExtensionNotFoundException
     */
    public function get(string $id): object
    {
        return $this->extensions->get($id);
    }

    public function has(string $id): bool
    {
        return $this->extensions->has($id);
    }

    /**
     * Creates local (webview-aware) event listener
     * based on the provided dispatcher.
     */
    private static function createEventListener(EventDispatcherInterface $dispatcher): EventListener
    {
        return new DelegateEventListener($dispatcher);
    }

    /**
     * Binds a PHP callback to a new global JavaScript function.
     *
     * Note: This is facade method of the {@see BindingsApi::bind()},
     *       that provides by the {@see $bindings} field. This means that
     *       calling `$webview->functions->bind(...)` should have the same effect.
     *
     * @api
     *
     * @param non-empty-string $function
     *
     * @throws FunctionAlreadyDefinedException in case of function binding error
     *
     * @deprecated please use `$webview->bindings->bind()` instead
     *
     * @uses BindingsApiInterface::bind() WebView Functions API
     */
    public function bind(string $function, \Closure $callback): void
    {
        /** @phpstan-ignore-next-line : Allow dynamic property access */
        $this->bindings->bind($function, $callback);
    }

    /**
     * Evaluates arbitrary JavaScript code.
     *
     * Note: This is facade method of the {@see ScriptsApi::eval()},
     *       that provides by the {@see $scripts} field. This means that
     *       calling `$webview->scripts->eval(...)` should have the same effect.
     *
     * @api
     *
     * @param string $code A JavaScript code for execution
     *
     * @deprecated please use `$webview->scripts->eval()` instead
     *
     * @uses ScriptsApiInterface::eval() WebView Scripts API
     */
    public function eval(#[Language('JavaScript')] string $code): void
    {
        /** @phpstan-ignore-next-line : Allow dynamic property access */
        $this->scripts->eval($code);
    }

    /**
     * Requests arbitrary data from webview using JavaScript code.
     *
     * Note: This is facade method of the {@see DataRetriever::get()},
     *       that provides by the {@see $data} field. This means that
     *       calling `$webview->requests->send(...)` should have the same effect.
     *
     * @api
     *
     * @param string $code A JavaScript code for execution
     *
     * @deprecated please use `$webview->data->get()` instead
     *
     * @uses DataRetrieverInterface::get() WebView Requests API
     */
    #[BlockingOperation]
    public function data(#[Language('JavaScript')] string $code, ?float $timeout = null): mixed
    {
        /** @phpstan-ignore-next-line : Allow dynamic property access */
        return $this->data->get($code, $timeout);
    }

    /**
     * Registers a new component with the given tag name and component class.
     *
     * @api
     *
     * @param non-empty-string $name The component name (tag)
     * @param class-string $component The fully qualified class name of the component
     *
     * @throws ComponentAlreadyDefinedException if a component with the given name is already registered
     * @throws WebComponentsApiException if any other registration error occurs
     *
     * @deprecated please use `$webview->components->add()` instead
     *
     * @uses WebComponentsApiInterface::add() WebView Web Components API
     */
    public function defineComponent(string $name, string $component): void
    {
        /** @phpstan-ignore-next-line : Allow dynamic property access */
        $this->components->add($name, $component);
    }

    /**
     * Go forward using current history.
     *
     * @api
     */
    public function forward(): void
    {
        $this->saucer->saucer_webview_forward($this->id->ptr);
    }

    /**
     * Go back using current history.
     *
     * @api
     */
    public function back(): void
    {
        $this->saucer->saucer_webview_back($this->id->ptr);
    }

    /**
     * Reload current layout.
     *
     * @api
     */
    public function reload(): void
    {
        $this->saucer->saucer_webview_reload($this->id->ptr);
    }

    /**
     * @internal for internal usage only
     */
    public function destroy(): void
    {
        $this->extensions->destroy();
        $this->listener->removeAllEventListeners();

        \gc_collect_cycles();
    }

    public function __destruct()
    {
        $this->destroy();
    }

    public function __get(string $name): object
    {
        return $this->extensions->get($name);
    }

    public function __isset(string $name): bool
    {
        return $this->extensions->has($name);
    }

    public function __set(string $name, mixed $value): void
    {
        $context = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'] ?? null;

        if ($context !== self::class) {
            throw new \Error(\sprintf('Cannot create dynamic property %s::$%s', static::class, $name));
        }

        /** @phpstan-ignore property.dynamicName */
        $this->$name = $value;
    }
}
