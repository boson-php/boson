<?php

declare(strict_types=1);

namespace Boson\WebView\Manager;

use Boson\Component\Saucer\SaucerInterface;
use Boson\Component\WeakType\ObservableWeakSet;
use Boson\Contracts\EventListener\EventListenerInterface;
use Boson\Dispatcher\DelegateEventListener;
use Boson\Dispatcher\EventListener;
use Boson\Dispatcher\EventListenerProvider;
use Boson\WebView\WebView;
use Boson\WebView\WebViewCreateInfo;
use Boson\Window\Event\WindowClosed;
use Boson\Window\Window;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

/**
 * Manages the lifecycle and collection of webviews in the window.
 *
 * Implements the webview collection interface and factory pattern,
 * providing functionality to create, track, and manage webviews throughout
 * their lifecycle.
 *
 * @template-implements \IteratorAggregate<array-key, WebView>
 */
final class WebViewManager implements
    EventListenerInterface,
    WebViewCollectionInterface,
    WebViewFactoryInterface,
    \IteratorAggregate
{
    use EventListenerProvider;

    /**
     * Gets default webview instance.
     *
     * It may be {@see null} in case of webview has been
     * closed (removed) earlier.
     */
    public private(set) ?WebView $default;

    /**
     * Contains a list of all webviews in use.
     *
     * @var \SplObjectStorage<WebView, mixed>
     */
    private readonly \SplObjectStorage $webviews;

    /**
     * Contains a list of subscriptions for webview destruction.
     *
     * @var ObservableWeakSet<WebView>
     */
    private readonly ObservableWeakSet $memory;

    /**
     * WebViews list aware event listener & dispatcher.
     */
    private readonly EventListener $listener;

    /**
     * WebView creator instance
     */
    private readonly WebViewHandlerFactory $factory;

    public function __construct(
        private readonly SaucerInterface $api,
        private readonly Window $window,
        WebViewCreateInfo $info,
        EventDispatcherInterface $dispatcher,
    ) {
        // Initialization Window Manager's fields and properties
        $this->webviews = $this->createWebViewsStorage();
        $this->memory = $this->createWindowsDestructorObserver();
        $this->listener = $this->createEventListener($dispatcher);
        $this->factory = $this->createWebViewHandlerFactory();

        // Register Window Manager's subsystems
        $this->registerDefaultEventListeners();
        $this->default = $this->createDefaultWebView($info);
    }

    /**
     * Creates a new instance of {@see \SplObjectStorage} for storing webview
     * instances.
     *
     * This storage is required to keep all webview objects in memory.
     *
     * @return \SplObjectStorage<WebView, mixed>
     */
    private function createWebViewsStorage(): \SplObjectStorage
    {
        /** @var \SplObjectStorage<WebView, mixed> */
        return new \SplObjectStorage();
    }

    /**
     * Creates a new instance of {@see ObservableWeakSet} for tracking webview
     * destruction.
     *
     * This set does NOT store objects in memory, but references the main
     * storage created by {@see createWebViewsStorage()}.
     *
     * @return ObservableWeakSet<WebView>
     */
    private function createWindowsDestructorObserver(): ObservableWeakSet
    {
        /** @var ObservableWeakSet<WebView> */
        return new ObservableWeakSet();
    }

    /**
     * Creates local (windows-aware) event listener
     * based on the provided dispatcher.
     */
    private function createEventListener(PsrEventDispatcherInterface $dispatcher): EventListener
    {
        return new DelegateEventListener($dispatcher);
    }

    /**
     * Creates a new webview handler factory
     */
    private function createWebViewHandlerFactory(): WebViewHandlerFactory
    {
        return new WebViewHandlerFactory($this->api, $this->window);
    }

    /**
     * Registers default event listeners for the webview manager.
     *
     * This method sets up handlers for window lifecycle events, such as
     * webview closure and default webview recalculation.
     */
    private function registerDefaultEventListeners(): void
    {
        // TODO
        return;

        $this->listener->addEventListener(WindowClosed::class, function (WindowClosed $event) {
            $this->webviews->offsetUnset($event->subject);

            // Recalculate default window in case of
            // previous default window was closed.
            if ($this->default === $event->subject) {
                $this->default = $this->windows->count() > 0 ? $this->windows->current() : null;
            }
        });
    }

    /**
     * Creates default window instance defined in default configuration
     */
    private function createDefaultWebView(WebViewCreateInfo $info): WebView
    {
        return $this->create($info);
    }

    public function create(WebViewCreateInfo $info = new WebViewCreateInfo()): WebView
    {
        $instance = $this->defer($info);

        try {
            return $instance;
        } finally {
            $this->initializeIfNotInitialized($instance);
        }
    }

    public function defer(WebViewCreateInfo $info = new WebViewCreateInfo()): WebView
    {
        $instance = $this->createWebViewProxy($info);

        $this->webviews->offsetSet($instance, $info);

        return $instance;
    }

    /**
     * Creates a webview proxy that will be initialized later.
     */
    private function createWebViewProxy(WebViewCreateInfo $info): WebView
    {
        /** @var WebView */
        return new \ReflectionClass(WebView::class)
            ->newLazyGhost(function (WebView $webview) use ($info): void {
                $this->onInitialize($webview, $info);
            });
    }

    /**
     * Calls after webview object has been release
     */
    private function onRelease(WebView $webview): void
    {
        //$this->api->saucer_webview_clear_scripts($window->id->ptr);
        //$this->api->saucer_webview_clear_embedded($window->id->ptr);
        //$this->api->saucer_free($window->id->ptr);

        // $this->listener->dispatch(new WindowDestroyed($window));
    }

    /**
     * Calls after webview object has been created
     */
    private function onCreate(WebView $webview): void
    {
        // $this->listener->dispatch(new WindowCreated($window));
    }

    private function onInitialize(WebView $webview, WebViewCreateInfo $info): void
    {
        $handle = $this->factory->create($info);

        $webview->__construct(
            saucer: $this->api,
            id: $handle,
            window: $this->window,
            info: $info,
            dispatcher: $this->listener,
        );

        $this->memory->watch($webview, $this->onRelease(...));

        $this->onCreate($webview);
    }

    private function initializeIfNotInitialized(WebView $webview): void
    {
        // Getting any object`s field will force initialization
        // of any proxy object.
        $webview->id;
    }

    public function boot(): void
    {
        foreach ($this->webviews as $webview) {
            $this->initializeIfNotInitialized($webview);
        }
    }

    public function getIterator(): \Traversable
    {
        return $this->webviews;
    }

    public function count(): int
    {
        return $this->webviews->count();
    }
}