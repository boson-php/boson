<?php

declare(strict_types=1);

namespace Boson\WebView\Manager;

use Boson\Component\Saucer\SaucerInterface;
use Boson\Component\WeakType\ObservableSet;
use Boson\Contracts\EventListener\EventListenerInterface;
use Boson\Dispatcher\DelegateEventListener;
use Boson\Dispatcher\EventListener;
use Boson\Dispatcher\EventListenerProvider;
use Boson\WebView\Exception\WindowDereferenceException;
use Boson\WebView\WebView;
use Boson\WebView\WebViewCreateInfo;
use Boson\Window\Window;
use Internal\Destroy\Destroyable;
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
    \IteratorAggregate,
    Destroyable
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
     * @var ObservableSet<WebView>
     */
    private readonly ObservableSet $webviews;

    /**
     * WebViews list aware event listener & dispatcher.
     */
    private readonly EventListener $listener;

    /**
     * WebView creator instance
     */
    private readonly WebViewHandlerFactory $factory;

    /**
     * Gets a reference to the parent window to which the
     * specified all children webview instances belongs.
     */
    private Window $window {
        /**
         * @throws WindowDereferenceException in case of parent window has been removed
         */
        get => $this->reference->get()
            ?? throw WindowDereferenceException::becauseNoParentWindow();
    }

    /**
     * @var \WeakReference<Window>
     */
    private readonly \WeakReference $reference;

    public function __construct(
        private readonly SaucerInterface $api,
        Window $parent,
        WebViewCreateInfo $info,
        EventDispatcherInterface $dispatcher,
    ) {
        $this->reference = \WeakReference::create($parent);

        // Initialization Window Manager's fields and properties
        $this->webviews = $this->createWebViewsStorage();
        $this->listener = $this->createEventListener($dispatcher);
        $this->factory = $this->createWebViewHandlerFactory();

        // Register Window Manager's subsystems
        $this->default = $this->create($info);
    }

    /**
     * Creates a new instance of {@see ObservableSet} for storing webview
     * instances and track its destruction.
     *
     * @return ObservableSet<WebView>
     */
    private function createWebViewsStorage(): ObservableSet
    {
        return new ObservableSet();
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
        return new WebViewHandlerFactory($this->api);
    }

    public function create(WebViewCreateInfo $info = new WebViewCreateInfo()): WebView
    {
        $instance = new WebView(
            saucer: $this->api,
            id: $this->factory->create($this->window, $info),
            parent: $this->window,
            info: $info,
            dispatcher: $this->listener,
        );

        $this->webviews->watch($instance, $this->onRelease(...));

        $this->onCreate($instance);

        return $instance;
    }

    /**
     * Calls after webview object has been release
     */
    private function onRelease(WebView $webview): void
    {
        // $this->listener->dispatch(new WindowDestroyed($window));
    }

    /**
     * Calls after webview object has been created
     */
    private function onCreate(WebView $webview): void
    {
        // $this->listener->dispatch(new WindowCreated($window));
    }

    /**
     * @internal for internal usage only
     */
    public function destroy(): void
    {
        /** @var WebView $webview */
        foreach ($this->webviews as $webview) {
            $this->webviews->detach($webview);

            $webview->destroy();
        }

        $this->default = null;
        $this->listener->removeAllEventListeners();

        \gc_collect_cycles();
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
