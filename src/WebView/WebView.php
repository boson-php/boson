<?php

declare(strict_types=1);

namespace Boson\WebView;

use Boson\Dispatcher\DelegateEventListener;
use Boson\Dispatcher\EventListener;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Binding\Exception\FunctionAlreadyDefinedException;
use Boson\WebView\Binding\WebViewFunctionsMap;
use Boson\WebView\Internal\WebViewEventHandler;
use Boson\WebView\Internal\WebViewSchemeHandler;
use Boson\WebView\Requests\WebViewRequests;
use Boson\WebView\Scripts\WebViewScriptsSet;
use Boson\Window\Window;
use FFI\CData;
use JetBrains\PhpStorm\Language;
use Psr\EventDispatcher\EventDispatcherInterface;

final class WebView
{
    /**
     * @var non-empty-string
     */
    private const string PRELOADED_SCRIPTS_PATTERN = __DIR__ . '/../../resources/dist/*.js';

    /**
     * Gets access to the listener of the webview events
     * and intention subscriptions.
     */
    public readonly EventListener $events;

    /**
     * Gets access to the Scripts API of the webview.
     *
     * Provides the ability to register a JavaScript code
     * in the webview.
     */
    public readonly WebViewScriptsSet $scripts;

    /**
     * Gets access to the Functions API of the webview.
     *
     * Provides the ability to register PHP functions
     * in the webview.
     */
    public readonly WebViewFunctionsMap $functions;

    /**
     * Gets access to the Requests API of the webview.
     *
     * Provides the ability to receive variant data from
     * the current document.
     */
    public readonly WebViewRequests $requests;

    /**
     * Contains webview URI instance.
     */
    public string $url {
        /**
         * Gets current webview URI instance.
         *
         * ```
         * echo $webview->url;          // http://example.com
         * ```
         */
        get {
            $result = $this->api->saucer_webview_url($this->ptr);

            try {
                return \FFI::string($result);
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
            $this->api->saucer_webview_set_url($this->ptr, (string) $value);
        }
    }

    /**
     * Load HTML content into the WebView.
     */
    public string $html {
        set(#[Language('HTML')] string|\Stringable $html) {
            $base64 = \base64_encode((string) $html);

            $this->url = \sprintf('data:text/html;base64,%s', $base64);
        }
    }

    /**
     * Gets webview status.
     */
    public private(set) WebViewState $state = WebViewState::Loading;

    /**
     * Internal window's webview pointer (handle).
     */
    private readonly CData $ptr;

    /**
     * Contains an internal bridge between {@see LibSaucer} events system
     * and the PSR {@see WebView::$events} dispatcher.
     *
     * @noinspection PhpPropertyOnlyWrittenInspection
     *
     * @phpstan-ignore property.onlyWritten
     */
    private readonly WebViewEventHandler $internalWebViewEventHandler;

    /**
     * Contains an internal bridge between {@see LibSaucer} scheme interception
     * system and the PSR {@see WebView::$events} dispatcher.
     *
     * @noinspection PhpPropertyOnlyWrittenInspection
     *
     * @phpstan-ignore property.onlyWritten
     */
    private readonly WebViewSchemeHandler $internalWebViewSchemeHandler;

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
        private readonly LibSaucer $api,
        /**
         * Gets parent application window instance to which
         * this webview instance belongs.
         */
        public readonly Window $window,
        /**
         * Gets information DTO about the webview with which it was created.
         */
        public readonly WebViewCreateInfo $info,
        EventDispatcherInterface $dispatcher,
    ) {
        $this->events = new DelegateEventListener($dispatcher);
        // The WebView handle pointer is the same as the Window pointer.
        $this->ptr = $this->window->id->ptr;

        $this->scripts = $this->createWebViewScriptsApi();
        $this->functions = $this->createFunctionsApi();
        $this->requests = $this->createRequestApi();

        $this->internalWebViewSchemeHandler = $this->createWebViewSchemeHandler();
        $this->internalWebViewEventHandler = $this->createWebViewEventHandler();

        $this->bootWebView();
    }

    private function createWebViewScriptsApi(): WebViewScriptsSet
    {
        return new WebViewScriptsSet(
            api: $this->api,
            webview: $this,
        );
    }

    private function createFunctionsApi(): WebViewFunctionsMap
    {
        return new WebViewFunctionsMap(
            scriptsApi: $this->scripts,
            events: $this->events,
        );
    }

    private function createRequestApi(): WebViewRequests
    {
        return new WebViewRequests($this);
    }

    private function createWebViewSchemeHandler(): WebViewSchemeHandler
    {
        return new WebViewSchemeHandler(
            api: $this->api,
            webview: $this,
            events: $this->events,
        );
    }

    private function createWebViewEventHandler(): WebViewEventHandler
    {
        return new WebViewEventHandler(
            api: $this->api,
            webview: $this,
            dispatcher: $this->events,
            state: $this->state,
        );
    }

    /**
     * Load configured payload to the webview
     */
    private function bootWebView(): void
    {
        $this->loadRuntimeScripts();

        foreach ($this->info->functions as $function => $callback) {
            $this->functions->bind($function, $callback);
        }

        foreach ($this->info->scripts as $script) {
            $this->scripts->add($script);
        }

        if ($this->info->url !== null) {
            $this->url = $this->info->url;
        }

        if ($this->info->html !== null) {
            $this->html = $this->info->html;
        }
    }

    /**
     * Loads predefined scripts list
     */
    private function loadRuntimeScripts(): void
    {
        /** @var list<non-empty-string> $scripts */
        $scripts = (array) \glob(self::PRELOADED_SCRIPTS_PATTERN);

        foreach ($scripts as $script) {
            if (\is_string($script) && \is_readable($script)) {
                $code = \file_get_contents($script);

                if (\is_string($code) && $code !== '') {
                    $this->scripts->preload($code, true);
                }
            }
        }
    }

    /**
     * Binds a PHP callback to a new global JavaScript function.
     *
     * Note: This is facade method of the {@see WebViewFunctionsMap::bind()},
     *       that provides by the {@see $functions} field. This means that
     *       calling `$webview->functions->bind(...)` should have the same effect.
     *
     * @api
     *
     * @uses WebViewFunctionsMap::bind() WebView Functions API
     *
     * @param non-empty-string $function
     *
     * @throws FunctionAlreadyDefinedException in case of function binding error
     */
    public function bind(string $function, \Closure $callback): void
    {
        $this->functions->bind($function, $callback);
    }

    /**
     * Evaluates arbitrary JavaScript code.
     *
     * Note: This is facade method of the {@see WebViewScriptsSet::eval()},
     *       that provides by the {@see $scripts} field. This means that
     *       calling `$webview->scripts->eval(...)` should have the same effect.
     *
     * @api
     *
     * @uses WebViewScriptsSet::eval() WebView Scripts API
     *
     * @param string $code A JavaScript code for execution
     */
    public function eval(#[Language('JavaScript')] string $code): void
    {
        $this->scripts->eval($code);
    }

    /**
     * Requests arbitrary data from webview using JavaScript code.
     *
     * Note: This is facade method of the {@see WebViewRequests::send()},
     *       that provides by the {@see $requests} field. This means that
     *       calling `$webview->requests->send(...)` should have the same effect.
     *
     * @api
     *
     * @uses WebViewRequests::send() WebView Requests API
     *
     * @param string $code A JavaScript code for execution
     */
    #[BlockingOperation]
    public function request(#[Language('JavaScript')] string $code): mixed
    {
        return $this->requests->send($code);
    }

    /**
     * Go forward using current history.
     *
     * @api
     */
    public function forward(): void
    {
        $this->api->saucer_webview_forward($this->ptr);
    }

    /**
     * Go back using current history.
     *
     * @api
     */
    public function back(): void
    {
        $this->api->saucer_webview_back($this->ptr);
    }

    /**
     * Reload current layout.
     *
     * @api
     */
    public function reload(): void
    {
        $this->api->saucer_webview_reload($this->ptr);
    }
}
