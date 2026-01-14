<?php

declare(strict_types=1);

namespace Boson\Window\Manager;

use Boson\Application;
use Boson\Component\Saucer\SaucerInterface;
use Boson\Component\WeakType\ObservableSet;
use Boson\Contracts\EventListener\EventListenerInterface;
use Boson\Dispatcher\DelegateEventListener;
use Boson\Dispatcher\EventListener;
use Boson\Dispatcher\EventListenerProvider;
use Boson\Window\Event\WindowClosed;
use Boson\Window\Event\WindowCreated;
use Boson\Window\Event\WindowDestroyed;
use Boson\Window\Window;
use Boson\Window\WindowCreateInfo;
use Internal\Destroy\Destroyable as DestroyableInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Manages the lifecycle and collection of windows in the application
 *
 * Implements the window collection interface and factory pattern,
 * providing functionality to create, track, and manage windows throughout
 * their lifecycle
 *
 * @template-implements \IteratorAggregate<array-key, Window>
 */
final class WindowManager implements
    EventListenerInterface,
    WindowCollectionInterface,
    WindowFactoryInterface,
    \IteratorAggregate,
    DestroyableInterface
{
    use EventListenerProvider;

    /**
     * Gets the default window instance
     *
     * It may be {@see null} in case of a window has been
     * closed (removed) earlier
     */
    public private(set) ?Window $default;

    /**
     * Contains a list of all windows in use and track window destruction
     *
     * @var ObservableSet<Window>
     */
    private readonly ObservableSet $windows;

    /**
     * Windows list aware event listener and dispatcher
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
        $this->windows = new ObservableSet();
        $this->listener = new DelegateEventListener($dispatcher);
        $this->factory = new WindowHandlerFactory($this->api, $this->app);

        $this->registerDefaultEventListeners();
        $this->default = $this->defer($info);
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
            $this->windows->detach($event->subject);

            // Recalculate a default window in case of
            // a previous default window was closed
            if ($this->default === $event->subject) {
                foreach ($this->windows as $window) {
                    $this->default = $window;
                    break;
                }
            }
        });
    }

    public function create(WindowCreateInfo $info = new WindowCreateInfo()): Window
    {
        $instance = $this->defer($info);

        $this->initializeIfNotInitialized($instance);

        return $instance;
    }

    public function defer(WindowCreateInfo $info = new WindowCreateInfo()): Window
    {
        /** @var Window $instance */
        $instance = new \ReflectionClass(Window::class)
            ->newLazyGhost(function (Window $window) use ($info): void {
                $this->onInitialize($window, $info);
            });

        $this->windows->watch($instance, $this->onRelease(...));

        return $instance;
    }

    /**
     * Calls after a window object has been release
     */
    private function onRelease(Window $window): void
    {
        $this->listener->dispatch(new WindowDestroyed($window));
    }

    /**
     * Calls after a window object has been created
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

        $this->onCreate($window);
    }

    private function initializeIfNotInitialized(Window $window): void
    {
        // Getting any object's field will force initialization
        // of any proxy object.
        $_ = $window->id;
    }

    public function boot(): void
    {
        /** @var Window $window */
        foreach ($this->windows as $window) {
            $this->initializeIfNotInitialized($window);
        }
    }

    /**
     * @internal for internal usage only
     */
    public function destroy(): void
    {
        /** @var Window $window */
        foreach ($this->windows as $window) {
            $this->windows->detach($window);
        }

        $this->default = null;
        $this->listener->removeAllEventListeners();

        \gc_collect_cycles();
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
