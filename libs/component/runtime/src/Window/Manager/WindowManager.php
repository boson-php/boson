<?php

declare(strict_types=1);

namespace Boson\Window\Manager;

use Boson\Application;
use Boson\Component\Saucer\SaucerInterface;
use Boson\Component\WeakType\ObservableWeakSet;
use Boson\Contracts\EventListener\EventListenerInterface;
use Boson\Dispatcher\DelegateEventListener;
use Boson\Dispatcher\EventListener;
use Boson\Dispatcher\EventListenerProvider;
use Boson\Window\Event\WindowClosed;
use Boson\Window\Event\WindowCreated;
use Boson\Window\Event\WindowDestroyed;
use Boson\Window\Window;
use Boson\Window\WindowCreateInfo;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

/**
 * Manages the lifecycle and collection of windows in the application.
 *
 * Implements the window collection interface and factory pattern,
 * providing functionality to create, track, and manage windows throughout
 * their lifecycle.
 *
 * @template-implements \IteratorAggregate<array-key, Window>
 */
final class WindowManager implements
    EventListenerInterface,
    WindowCollectionInterface,
    WindowFactoryInterface,
    \IteratorAggregate
{
    use EventListenerProvider;

    /**
     * Gets default window instance.
     *
     * It may be {@see null} in case of window has been
     * closed (removed) earlier.
     */
    public private(set) ?Window $default;

    /**
     * Contains a list of all windows in use.
     *
     * @var \SplObjectStorage<Window, mixed>
     */
    private readonly \SplObjectStorage $windows;

    /**
     * Contains a list of subscriptions for window destruction.
     *
     * @var ObservableWeakSet<Window>
     */
    private readonly ObservableWeakSet $memory;

    /**
     * Windows list aware event listener & dispatcher.
     */
    private readonly EventListener $listener;

    /**
     * Window creator instance
     */
    private readonly WindowHandlerFactory $factory;

    public function __construct(
        private readonly SaucerInterface $api,
        private readonly Application $app,
        WindowCreateInfo $info,
        EventDispatcherInterface $dispatcher,
    ) {
        // Initialization Window Manager's fields and properties
        $this->windows = $this->createWindowsStorage();
        $this->memory = $this->createWindowsDestructorObserver();
        $this->listener = $this->createEventListener($dispatcher);
        $this->factory = $this->createWindowHandlerFactory($api, $app);

        // Register Window Manager's subsystems
        $this->registerDefaultEventListeners();
        $this->default = $this->createDefaultWindow($info);
    }

    /**
     * Creates a new instance of {@see \SplObjectStorage} for storing window
     * instances.
     *
     * This storage is required to keep all window objects in memory.
     *
     * @return \SplObjectStorage<Window, mixed>
     */
    private function createWindowsStorage(): \SplObjectStorage
    {
        /** @var \SplObjectStorage<Window, mixed> */
        return new \SplObjectStorage();
    }

    /**
     * Creates a new instance of {@see ObservableWeakSet} for tracking window
     * destruction.
     *
     * This set does NOT store objects in memory, but references the main
     * storage created by {@see createWindowsStorage()}.
     *
     * @return ObservableWeakSet<Window>
     */
    private function createWindowsDestructorObserver(): ObservableWeakSet
    {
        /** @var ObservableWeakSet<Window> */
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
     * Creates a new window handler factory
     */
    private function createWindowHandlerFactory(): WindowHandlerFactory
    {
        return new WindowHandlerFactory($this->api, $this->app);
    }

    /**
     * Registers default event listeners for the window manager.
     *
     * This method sets up handlers for window lifecycle events, such as
     * window closure and default window recalculation.
     */
    private function registerDefaultEventListeners(): void
    {
        $this->listener->addEventListener(WindowClosed::class, function (WindowClosed $event) {
            $this->windows->offsetUnset($event->subject);

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
    private function createDefaultWindow(WindowCreateInfo $info): Window
    {
        return $this->defer($info);
    }

    public function create(WindowCreateInfo $info = new WindowCreateInfo()): Window
    {
        $instance = $this->defer($info);

        try {
            return $instance;
        } finally {
            $this->initializeIfNotInitialized($instance);
        }
    }

    public function defer(WindowCreateInfo $info = new WindowCreateInfo()): Window
    {
        $instance = $this->createWindowProxy($info);

        $this->windows->offsetSet($instance, $info);

        return $instance;
    }

    /**
     * Creates a window proxy that will be initialized later.
     */
    private function createWindowProxy(WindowCreateInfo $info): Window
    {
        /** @var Window */
        return new \ReflectionClass(Window::class)
            ->newLazyGhost(function (Window $window) use ($info): void {
                $this->onInitialize($window, $info);
            });
    }

    /**
     * Calls after window object has been release
     */
    private function onRelease(Window $window): void
    {
        //$this->api->saucer_webview_clear_scripts($window->id->ptr);
        //$this->api->saucer_webview_clear_embedded($window->id->ptr);
        //$this->api->saucer_free($window->id->ptr);

        $this->listener->dispatch(new WindowDestroyed($window));
    }

    /**
     * Calls after window object has been created
     */
    private function onCreate(Window $window): void
    {
        $this->listener->dispatch(new WindowCreated($window));
    }

    private function onInitialize(Window $window, WindowCreateInfo $info): void
    {
        $handle = $this->factory->create($info);

        $window->__construct(
            saucer: $this->api,
            id: $handle,
            app: $this->app,
            info: $info,
            dispatcher: $this->listener,
        );

        $this->memory->watch($window, $this->onRelease(...));

        $this->onCreate($window);
    }

    private function initializeIfNotInitialized(Window $window): void
    {
        // Getting any object`s field will force initialization
        // of any proxy object.
        $window->id;
    }

    public function boot(): void
    {
        foreach ($this->windows as $window) {
            $this->initializeIfNotInitialized($window);
        }
    }

    public function getIterator(): \Traversable
    {
        return $this->windows;
    }

    public function count(): int
    {
        return $this->windows->count();
    }
}
