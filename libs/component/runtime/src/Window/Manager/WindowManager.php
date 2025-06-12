<?php

declare(strict_types=1);

namespace Boson\Window\Manager;

use Boson\Application;
use Boson\Dispatcher\DelegateEventListener;
use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Dispatcher\EventListener;
use Boson\Dispatcher\EventListenerInterface;
use Boson\Dispatcher\EventListenerProvider;
use Boson\Dispatcher\EventListenerProviderInterface;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Component\WeakType\ObservableWeakSet;
use Boson\Window\Event\WindowClosed;
use Boson\Window\Event\WindowCreated;
use Boson\Window\Window;
use Boson\Window\WindowCreateInfo;
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
    EventListenerProviderInterface,
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
     * Gets access to the listener of ANY window events
     * and intention subscriptions.
     */
    public readonly EventListenerInterface $events;

    /**
     * Windows list aware event dispatcher.
     */
    private readonly EventDispatcherInterface $dispatcher;

    public function __construct(
        private readonly LibSaucer $api,
        private readonly Application $app,
        WindowCreateInfo $info,
        EventDispatcherInterface $dispatcher,
    ) {
        // Initialization Window Manager's fields and properties
        $this->windows = self::createWindowsStorage();
        $this->memory = self::createWindowsDestructorObserver();
        $this->events = $this->dispatcher = self::createEventListener($dispatcher);

        // Initialization of Window Manager's API
        // ...

        // Register Window Manager's subsystems
        $this->registerDefaultEventListeners();

        // Create default Window proxy instance
        $this->default = $this->create($info, true);
    }

    /**
     * Creates a new instance of {@see \SplObjectStorage} for storing window
     * instances.
     *
     * This storage is required to keep all window objects in memory.
     *
     * @return \SplObjectStorage<Window, mixed>
     */
    private static function createWindowsStorage(): \SplObjectStorage
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
    private static function createWindowsDestructorObserver(): ObservableWeakSet
    {
        /** @var ObservableWeakSet<Window> */
        return new ObservableWeakSet();
    }

    /**
     * Creates local (windows-aware) event listener
     * based on the provided dispatcher.
     */
    private static function createEventListener(PsrEventDispatcherInterface $dispatcher): EventListener
    {
        return new DelegateEventListener($dispatcher);
    }

    /**
     * Registers default event listeners for the window manager.
     *
     * This method sets up handlers for window lifecycle events, such as
     * window closure and default window recalculation.
     */
    private function registerDefaultEventListeners(): void
    {
        $this->events->addEventListener(WindowClosed::class, function (WindowClosed $event) {
            $this->windows->detach($event->subject);

            // Recalculate default window in case of
            // previous default window was closed.
            if ($this->default === $event->subject) {
                $this->default = $this->windows->count() > 0 ? $this->windows->current() : null;
            }
        });
    }

    public function create(WindowCreateInfo $info = new WindowCreateInfo(), bool $defer = false): Window
    {
        $instance = $defer
            ? $this->createWindowProxy($info)
            : $this->createWindowInstance($info);

        $this->windows->attach($instance, $info);

        return $instance;
    }

    /**
     * Creates a window proxy that will be initialized later.
     */
    private function createWindowProxy(WindowCreateInfo $info): Window
    {
        /** @var Window */
        return new \ReflectionClass(Window::class)
            ->newLazyProxy(function() use ($info): Window {
                $instance = $this->createWindowInstance($info);

                $this->swapWindowProxy($info, $instance);

                return $instance;
            });
    }

    /**
     * Swaps a window proxy with its actual instance.
     *
     * The problem is that the proxy ID in the storage and the real instance
     * are different. Therefore, it is necessary to change the window proxy
     * to the real instance after it initializing.
     */
    private function swapWindowProxy(WindowCreateInfo $info, Window $window): void
    {
        foreach ($this->windows as $proxy) {
            if ($this->windows->getInfo() === $info) {
                $this->windows->detach($proxy);
                $this->windows->attach($window, $info);

                return;
            }
        }
    }

    /**
     * Creates a new real window instance with the given information.
     */
    private function createWindowInstance(WindowCreateInfo $info): Window
    {
        $window = new Window(
            api: $this->api,
            app: $this->app,
            info: $info,
            dispatcher: $this->events,
        );

        $this->memory->watch($window, function (Window $window): void {
            $this->api->saucer_webview_clear_scripts($window->id->ptr);
            $this->api->saucer_webview_clear_embedded($window->id->ptr);
            $this->api->saucer_free($window->id->ptr);
        });

        $this->dispatcher->dispatch(new WindowCreated($window));

        return $window;
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
