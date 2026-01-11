<?php

declare(strict_types=1);

namespace Boson;

use Boson\Component\Saucer\Saucer;
use Boson\Component\Saucer\SaucerInterface;
use Boson\Contracts\EventListener\EventListenerInterface;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\DelegateEventListener;
use Boson\Dispatcher\EventListener;
use Boson\Dispatcher\EventListenerProvider;
use Boson\Event\ApplicationStarted;
use Boson\Event\ApplicationStarting;
use Boson\Event\ApplicationStopped;
use Boson\Event\ApplicationStopping;
use Boson\Exception\ApplicationException;
use Boson\Exception\NoDefaultWindowException;
use Boson\Extension\Exception\ExtensionNotFoundException;
use Boson\Extension\Registry;
use Boson\Internal\Poller\SaucerPoller;
use Boson\Poller\PollerInterface;
use Boson\Shared\Marker\BlockingOperation;
use Boson\Shared\Marker\RequiresDealloc;
use Boson\WebView\Manager\WebViewManager;
use Boson\WebView\WebView;
use Boson\Window\Event\WindowClosed;
use Boson\Window\Manager\WindowManager;
use Boson\Window\Window;
use FFI\CData;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @template-implements IdentifiableInterface<ApplicationId>
 */
#[\AllowDynamicProperties]
class Application implements
    IdentifiableInterface,
    EventListenerInterface,
    ContainerInterface
{
    use EventListenerProvider;

    /**
     * List of error types that block the application
     * from starting automatically.
     *
     * @var non-empty-list<int>
     */
    private const array NOT_RUNNABLE_ERROR_TYPES = [
        \E_ERROR,
        \E_PARSE,
        \E_CORE_ERROR,
        \E_COMPILE_ERROR,
        \E_USER_ERROR,
    ];

    /**
     * Unique application identifier.
     *
     * It is worth noting that the destruction of this object
     * from memory (deallocation using PHP GC) means the physical
     * destruction of all data associated with it, including unmanaged.
     *
     * @api
     */
    public readonly ApplicationId $id;

    /**
     * Gets windows list and methods for working with windows.
     *
     * @api
     */
    public readonly WindowManager $windows;

    /**
     * Provides more convenient and faster access to the {@see WindowManager::$default}
     * subsystem from child {@see $windows} property.
     *
     * @api
     */
    public Window $window {
        /**
         * Gets the default window of the application.
         *
         * @throws NoDefaultWindowException in case the default window was
         *         already closed and removed earlier
         */
        get => $this->windows->default
            ?? throw NoDefaultWindowException::becauseNoDefaultWindow();
    }

    /**
     * Provides more convenient and faster access to the {@see Window::$webviews}
     * subsystem from child {@see $window} property.
     *
     * @api
     */
    public WebViewManager $webviews {
        /**
         * Gets the webview manager of the default application window.
         *
         * @throws NoDefaultWindowException in case the default window was
         *         already closed and removed earlier
         */
        get => $this->window->webviews;
    }

    /**
     * Provides more convenient and faster access to the {@see Window::$webview}
     * subsystem from {@see $window} property.
     *
     * @api
     */
    public WebView $webview {
        /**
         * Gets the WebView instance associated with the default window.
         *
         * @throws NoDefaultWindowException in case the default window was
         *         already closed and removed earlier
         */
        get => $this->window->webview;
    }

    /**
     * Gets debug mode of an application.
     *
     * Unlike {@see ApplicationCreateInfo::$debug}, it contains
     * a REAL debug value, including possibly automatically derived.
     *
     * Contains {@see true} in case of debug mode
     * is enabled or {@see false} instead.
     *
     * @api
     */
    public readonly bool $isDebug;

    /**
     * Gets running state of an application.
     *
     * Contains {@see true} in case of application is running
     * or {@see false} instead.
     *
     * @api
     */
    public private(set) bool $isRunning = false;

    /**
     * Shared WebView API library.
     *
     * @api
     *
     * @internal Not safe, you can get segfault, use
     *           this low-level API at your own risk!
     */
    public readonly SaucerInterface $saucer;

    /**
     * An application poller interface.
     *
     * @api
     */
    public readonly PollerInterface $poller;

    /**
     * List of application extensions.
     *
     * @var Registry<Application>
     */
    private readonly Registry $extensions;

    /**
     * Application-aware event listener & dispatcher.
     */
    private readonly EventListener $listener;

    /**
     * @param EventDispatcherInterface|null $dispatcher an optional event
     *        dispatcher for handling application events
     */
    public function __construct(
        /**
         * Gets an information DTO about the application
         * with which it was created.
         */
        public readonly ApplicationCreateInfo $info = new ApplicationCreateInfo(),
        ?EventDispatcherInterface $dispatcher = null,
        /**
         * @var array<array-key, mixed>
         *
         * @deprecated doesn't affect anything anymore and will be removed in future versions
         */
        private readonly array $bootHandlers = [],
        /**
         * @var array<array-key, mixed>
         *
         * @deprecated doesn't affect anything anymore and will be removed in future versions
         */
        private readonly array $quitHandlers = [],
        /**
         * @var array<array-key, mixed>
         *
         * @deprecated doesn't affect anything anymore and will be removed in future versions
         */
        private readonly array $deferRunners = [],
    ) {
        // Initialization Application's fields and properties
        $this->isDebug = $this->createIsDebugParameter($info->debug);
        $this->listener = $this->createEventListener($dispatcher);

        // Kernel initialization
        $this->saucer = $this->createLibSaucer($info->library);
        $this->id = $this->createApplicationId($this->saucer, $this->info->name);
        $this->windows = $this->createWindowManager($this->saucer, $this, $info, $this->listener);
        $this->poller = $this->createApplicationPoller($this->saucer);

        // Initialization of Application's API
        $this->extensions = new Registry($this, $this->listener, $info->extensions);
        foreach ($this->extensions->boot() as $property => $extension) {
            // Direct access to dynamic property is 5+ times
            // faster than magic `__get` call.
            $this->__set($property, $extension);
        }

        // Register Application's subsystems
        $this->registerSchemes();
        $this->registerDefaultEventListeners();
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
     * Create an application event-loop.
     */
    protected function createApplicationPoller(SaucerInterface $saucer): PollerInterface
    {
        $poller = new SaucerPoller($this->id, $saucer);

        $poller->defer(function (): void {
            $this->isRunning = true;

            $this->listener->dispatch(new ApplicationStarted($this));
        });

        return $poller;
    }

    /**
     * Creates a new instance of {@see SaucerInterface} that provides access to the
     * native WebView API library. The returned object allows interacting with
     * the underlying WebView (Saucer) functionality through FFI bindings.
     *
     * The library path can be automatically detected if not explicitly specified.
     *
     * This method is responsible for initializing the core WebView functionality
     * that powers the application's window and web content capabilities.
     *
     * @param non-empty-string|null $library Optional path to the WebView library
     */
    protected function createLibSaucer(?string $library): SaucerInterface
    {
        if ($library !== null) {
            return new Saucer($library);
        }

        return Saucer::createFromGlobals();
    }

    /**
     * Resolves the debug mode state for the application based on the provided
     * configuration and environment settings.
     *
     * This method uses php.ini dev mode detection to determine the actual debug
     * state, taking into account both explicit configuration and automatic
     * environment detection.
     */
    protected function createIsDebugParameter(?bool $debug): bool
    {
        if ($debug === null) {
            $debug = false;

            /**
             * Enable debug mode if "zend.assertions" is 1.
             *
             * @link https://www.php.net/manual/en/function.assert.php
             */
            assert($debug = true);
        }

        return $debug;
    }

    /**
     * Creates a new window manager instance.
     *
     * This method initializes and returns a {@see WindowManager} object
     * that is responsible for managing all application windows.
     */
    protected function createWindowManager(
        SaucerInterface $api,
        Application $app,
        ApplicationCreateInfo $info,
        EventDispatcherInterface $dispatcher,
    ): WindowManager {
        return new WindowManager(
            api: $api,
            app: $app,
            info: $info->window,
            dispatcher: $dispatcher,
        );
    }

    /**
     * Register list of protocol names that will be
     * intercepted by the application.
     */
    private function registerSchemes(): void
    {
        foreach ($this->info->schemes as $scheme) {
            $this->saucer->saucer_webview_register_scheme($scheme);
        }
    }

    /**
     * Registers default event listeners for the application.
     *
     * This includes handling window close events.
     */
    private function registerDefaultEventListeners(): void
    {
        $this->listener->addEventListener(WindowClosed::class, $this->onWindowClose(...));
        $this->listener->addEventListener(ApplicationStarted::class, $this->onApplicationStarted(...));
    }

    /**
     * Handles the window close event.
     *
     * If {@see $quitOnClose} is enabled ({@see true}) and
     * all windows are closed, the application will quit.
     */
    private function onWindowClose(): void
    {
        if ($this->info->quitOnClose && $this->windows->count() === 0) {
            $this->quit();
        }
    }

    /**
     * Handles an application started event.
     *
     * Resolve main window lazy proxy (facade).
     */
    private function onApplicationStarted(): void
    {
        $this->windows->boot();
    }

    /**
     * Creates local (application-aware) event listener
     * based on the provided dispatcher.
     */
    protected function createEventListener(?EventDispatcherInterface $dispatcher): EventListener
    {
        if ($dispatcher === null) {
            return new EventListener();
        }

        return new DelegateEventListener($dispatcher);
    }

    /**
     * Creates a new application ID
     *
     * @param non-empty-string $name
     */
    protected function createApplicationId(SaucerInterface $api, string $name): ApplicationId
    {
        return ApplicationId::fromAppHandle(
            api: $api,
            handle: $this->createApplicationPointer($api, $name),
            name: $name,
        );
    }

    /**
     * Creates a new application instance pointer.
     *
     * @param non-empty-string $name
     */
    #[RequiresDealloc]
    protected function createApplicationPointer(SaucerInterface $api, string $name): CData
    {
        $options = $this->createApplicationOptionsPointer($api, $name);

        try {
            $result = $api->saucer_application_new($options, \FFI::addr(
                $error = $this->saucer->new('int'),
            ));

            if ($error->cdata !== 0) {
                throw new ApplicationException('An internal error occurred while creating application', $error->cdata);
            }

            return $result;
        } finally {
            $api->saucer_application_options_free($options);
        }
    }

    /**
     * Creates a new application options pointer.
     *
     * @param non-empty-string $name
     */
    #[RequiresDealloc]
    protected function createApplicationOptionsPointer(SaucerInterface $api, string $name): CData
    {
        return $api->saucer_application_options_new($name);
    }

    /**
     * Dispatches an intention to launch an application and returns a {@see bool}
     * result: whether to start the application or not.
     */
    private function shouldNotStart(): bool
    {
        $error = \error_get_last();

        // The application cannot be run if there are errors.
        if (\in_array($error['type'] ?? 0, self::NOT_RUNNABLE_ERROR_TYPES, true)) {
            return true;
        }

        $this->listener->dispatch($intention = new ApplicationStarting($this));

        return $intention->isCancelled;
    }

    /**
     * Runs the application, starting the main event loop.
     *
     * This method blocks main thread until the
     * application is quit.
     *
     * @api
     */
    #[BlockingOperation]
    public function run(): void
    {
        if ($this->isRunning || $this->shouldNotStart()) {
            return;
        }

        do {
            $this->poller->next();
            /** @phpstan-ignore doWhile.alwaysFalse */
        } while ($this->isRunning);
    }

    /**
     * Dispatches an intention to stop an application and returns a {@see bool}
     * result: whether to stop the application or not.
     */
    private function shouldNotStop(): bool
    {
        $this->listener->dispatch($intention = new ApplicationStopping($this));

        return $intention->isCancelled;
    }

    /**
     * Quits the application, stopping the main
     * loop and releasing resources.
     *
     * @api
     */
    public function quit(): void
    {
        if ($this->shouldNotStop()) {
            return;
        }

        $this->isRunning = false;
        $this->saucer->saucer_application_quit($this->id->ptr);

        $this->listener->dispatch(new ApplicationStopped($this));
    }

    /**
     * Destructor for the Application class.
     *
     * Ensures that all resources are properly released
     * when the application is destroyed.
     */
    public function __destruct()
    {
        $this->quit();

        $this->windows->destroy();
        $this->extensions->destroy();

        var_dump(__METHOD__);
        $this->saucer->saucer_application_quit($this->id->ptr);
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
