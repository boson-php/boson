<?php

declare(strict_types=1);

namespace Boson\Window;

use Boson\Application;
use Boson\Component\Saucer\SaucerInterface;
use Boson\Component\Saucer\WindowDecoration as SaucerWindowDecoration;
use Boson\Component\Saucer\WindowEdge as SaucerWindowEdge;
use Boson\Contracts\EventListener\EventListenerInterface;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\DelegateEventListener;
use Boson\Dispatcher\EventListener;
use Boson\Dispatcher\EventListenerProvider;
use Boson\Extension\Exception\ExtensionNotFoundException;
use Boson\Extension\Registry;
use Boson\WebView\Manager\WebViewManager;
use Boson\WebView\WebView;
use Boson\Window\Event\WindowClosed;
use Boson\Window\Event\WindowDecorationChanged;
use Boson\Window\Event\WindowMaximized;
use Boson\Window\Event\WindowMinimized;
use Boson\Window\Event\WindowStateChanged;
use Boson\Window\Exception\WebViewDereferenceException;
use Boson\Window\Internal\Size\ManagedWindowMaxBounds;
use Boson\Window\Internal\Size\ManagedWindowMinBounds;
use Boson\Window\Internal\Size\ManagedWindowSize;
use Boson\Window\Manager\WindowFactoryInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @template-implements IdentifiableInterface<WindowId>
 */
#[\AllowDynamicProperties]
final class Window implements
    IdentifiableInterface,
    EventListenerInterface,
    ContainerInterface
{
    use EventListenerProvider;

    /**
     * Gets webviews list and methods for working with webviews.
     *
     * @api
     */
    public readonly WebViewManager $webviews;

    /**
     * Provides more convenient and faster access to the {@see WebViewManager::$default}
     * subsystem from child {@see $webviews} property.
     *
     * @api
     */
    public WebView $webview {
        /**
         * Gets the default webview of the window.
         *
         * @throws WebViewDereferenceException in case the default webview was
         *         already closed and removed earlier
         */
        get => $this->webviews->default
            ?? throw WebViewDereferenceException::becauseNoDefaultWebView();
    }

    /**
     * The title of the specified window encoded as UTF-8.
     *
     * @api
     */
    public string $title {
        get => $this->title ??= $this->getCurrentWindowTitle();
        set {
            $this->saucer->saucer_window_set_title($this->id->ptr, $this->title = $value);
        }
    }

    /**
     * Gets window state.
     *
     * @api
     */
    public private(set) WindowState $state = WindowState::DEFAULT {
        get => $this->state;
        set {
            [$previous, $this->state] = [$this->state, $value];

            if ($previous === $value) {
                return;
            }

            $this->listener->dispatch(new WindowStateChanged(
                subject: $this,
                state: $value,
                previous: $previous,
            ));
        }
    }

    /**
     * Provides window decorations configs.
     *
     * @api
     */
    public WindowDecoration $decoration {
        /**
         * Gets the current window decoration value.
         *
         * ```
         * if ($window->decoration === WindowDecoration::Default) {
         *     echo 'Window is default';
         * } else {
         *     echo 'Window has custom decoration';
         * }
         * ```
         */
        get => $this->decoration ??= $this->info->decoration;
        /**
         * Updates current window decorations mode.
         *
         * ```
         * // Toggle decorations
         * $window->decoration = $window->decoration === WindowDecoration::Frameless
         *     ? WindowDecoration::Default
         *     : WindowDecoration::Frameless;
         * ```
         */
        set {
            [$previous, $this->decoration] = [$this->decoration, $value];

            if ($value === $previous) {
                return;
            }

            $this->saucer->saucer_window_set_decorations($this->id->ptr, match ($value) {
                WindowDecoration::Full => SaucerWindowDecoration::SAUCER_WINDOW_DECORATION_FULL,
                WindowDecoration::Frameless => SaucerWindowDecoration::SAUCER_WINDOW_DECORATION_PARTIAL,
                WindowDecoration::None => SaucerWindowDecoration::SAUCER_WINDOW_DECORATION_NONE,
            });

            $this->listener->dispatch(new WindowDecorationChanged(
                subject: $this,
                decoration: $value,
                previous: $previous,
            ));
        }
    }

    /**
     * Gets the current window size
     *
     * @api
     */
    public MutableSizeInterface $size {
        /**
         * Returns mutable {@see MutableSizeInterface} window size value object.
         *
         * ```
         * echo $window->size; // Size(640 × 480)
         * ```
         *
         * Since the property returns mutable window size, they can be
         * changed explicitly.
         *
         * ```
         * $window->size->width = 640;
         * $window->size->height = 648;
         * ```
         *
         * Or using simultaneously update.
         *
         * ```
         * $window->size->update(640, 480);
         * ```
         *
         * In case of size is NOT initialized creates a new instance of
         * {@see ManagedWindowSize} that wraps the native window size
         * functionality. The returned object allows reading and modifying
         * the window's width and height through a managed interface.
         *
         * The size is managed by the native window system, and any changes
         * to the size through this interface will be reflected in the actual
         * window dimensions.
         */
        get => $this->size ??= new ManagedWindowSize($this->saucer, $this->id->ptr);
        /**
         * Allows updating window size using any {@see SizeInterface}
         * (for example {@see Size}) instance.
         *
         * ```
         * $window->size = new Size(640, 480);
         * ```
         *
         * The sizes can also be passed between different window instances
         * and window properties.
         *
         * ```
         * $window1->size = $window2->size;
         * ```
         */
        set(SizeInterface $size) {
            $this->size->update($size->width, $size->height);
        }
    }

    /**
     * Contains minimum size bounds of the window.
     *
     * @api
     */
    public MutableSizeInterface $min {
        /**
         * Returns mutable {@see MutableSizeInterface} minimum size bounds
         * of the window.
         *
         * ```
         * echo $window->min; // Size(0 × 0)
         * ```
         *
         * Since the property returns mutable minimum size bounds,
         * they can be changed explicitly.
         *
         * ```
         * $window->min->width = 640;
         * $window->min->height = 648;
         * ```
         *
         * Or using simultaneously update.
         *
         * ```
         * $window->min->update(640, 480);
         * ```
         *
         * In case of min size is NOT initialized creates a new instance of
         * {@see ManagedWindowMinBounds} that wraps the native window minimum
         * size bounds functionality. The returned object allows reading and
         * modifying the window's minimum width and height through a managed
         * interface.
         *
         * The minimum size bounds are managed by the native window system,
         * and any changes to the bounds through this interface will be
         * reflected in the actual window constraints.
         */
        get => $this->min ??= new ManagedWindowMinBounds($this->saucer, $this->id->ptr);
        /**
         * Allows updating window minimal size bound using any
         * {@see SizeInterface} (for example {@see Size}) instance.
         *
         * ```
         * $window->min = new Size(640, 480);
         * ```
         *
         * The sizes can also be passed between different window instances
         * and window properties.
         *
         * ```
         * $window->min = $window->size;
         * ```
         */
        set(SizeInterface $size) {
            $this->min->update($size->width, $size->height);
        }
    }

    /**
     * Contains maximum size bounds of the window.
     *
     * @api
     */
    public MutableSizeInterface $max {
        /**
         * Returns mutable {@see MutableSizeInterface} maximum size bounds
         * of the window.
         *
         * ```
         * echo $window->max; // Size(5142 × 1462)
         * ```
         *
         * Since the property returns mutable maximum size bounds,
         * they can be changed explicitly.
         *
         * ```
         * $window->max->width = 640;
         * $window->max->height = 648;
         * ```
         *
         * Or using simultaneously update.
         *
         * ```
         * $window->max->update(640, 480);
         * ```
         *
         * In case of max size is NOT initialized creates a new instance of
         * {@see ManagedWindowMaxBounds} that wraps the native window maximum
         * size bounds functionality. The returned object allows reading and
         * modifying the window's maximum width and height through a managed
         * interface.
         *
         * The maximum size bounds are managed by the native window system, and
         * any changes to the bounds through this interface will be reflected
         * in the actual window constraints.
         */
        get => $this->max ??= new ManagedWindowMaxBounds($this->saucer, $this->id->ptr);
        /**
         * Allows to update window maximal size bound using any
         * {@see SizeInterface} (for example {@see Size}) instance.
         *
         * ```
         * $window->max = new Size(640, 480);
         * ```
         *
         * The sizes can also be passed between different window instances
         * and window properties.
         *
         * ```
         * $window->max = $window->size;
         * ```
         */
        set(SizeInterface $size) {
            $this->max->update($size->width, $size->height);
        }
    }

    /**
     * Contains a window visibility option.
     *
     * @api
     */
    public bool $isVisible {
        /**
         * Gets current window visibility state.
         *
         * ```
         * if ($window->isVisible) {
         *     echo 'Window is visible';
         * } else {
         *     echo 'Window is hidden';
         * }
         * ```
         */
        get => $this->saucer->saucer_window_visible($this->id->ptr);
        /**
         * Show the window in case of property will be set to {@see true}
         * or hide in case of {@see false}.
         *
         * ```
         * // Show window
         * $window->isVisible = true;
         *
         * // Hide window
         * $window->isVisible = false;
         * ```
         */
        set {
            if ($value) {
                $this->saucer->saucer_window_show($this->id->ptr);
            } else {
                $this->saucer->saucer_window_hide($this->id->ptr);
            }
        }
    }

    /**
     * Contains window "always on top" option.
     *
     * @api
     */
    public bool $isAlwaysOnTop {
        /**
         * Gets current window "always on top" option.
         *
         * ```
         * if ($window->isAlwaysOnTop) {
         *     echo 'Window is always on top';
         * } else {
         *     echo 'Window is not always on top';
         * }
         * ```
         */
        get => $this->saucer->saucer_window_always_on_top($this->id->ptr);
        /**
         * Sets window "always on top" feature in case of property was be set
         * to {@see true} or disable this feature in case of {@see false}.
         *
         * ```
         * // Make window always on top
         * $window->isAlwaysOnTop = true;
         *
         * // Disable window always on top feature
         * $window->isVisible = false;
         * ```
         */
        set {
            $this->saucer->saucer_window_set_always_on_top($this->id->ptr, $value);
        }
    }

    /**
     * Contains window "click through" option.
     *
     * @api
     */
    public bool $isClickThrough {
        /**
         * Gets current window "click through" option.
         *
         * ```
         * if ($window->isClickThrough) {
         *     echo 'Window DOES NOT intercept mouse events';
         * } else {
         *     echo 'Window intercepts mouse events';
         * }
         * ```
         */
        get => $this->saucer->saucer_window_click_through($this->id->ptr);
        /**
         * Sets window "click through" feature in case of property was be set
         * to {@see true} or disable this feature in case of {@see false}.
         *
         * ```
         * // MMakes the window inaccessible for mouse control
         * $window->isClickThrough = true;
         *
         * // Disable "click through" feature
         * $window->isClickThrough = false;
         * ```
         */
        set {
            $this->saucer->saucer_window_set_click_through($this->id->ptr, $value);
        }
    }

    /**
     * Gets current window closed state.
     *
     * ```
     * if ($window->isClosed) {
     *     echo 'Window is closed';
     * } else {
     *     echo 'Window is not closed';
     * }
     * ```
     *
     * @api
     */
    public private(set) bool $isClosed = false;

    /**
     * Window aware event listener and dispatcher.
     */
    private readonly EventListener $listener;

    /**
     * List of window extensions.
     *
     * @var Registry<Window>
     */
    private readonly Registry $extensions;

    /**
     * @internal Please do not use the constructor directly. There is a
     *           corresponding {@see WindowFactoryInterface::create()} method
     *           for creating new windows, which ensures safe creation.
     *           ```
     *           $app = new Application();
     *
     *           // Should be used instead of calling the constructor
     *           $window = $app->windows->create();
     *           ```
     */
    public function __construct(
        /**
         * Contains a shared WebView API library.
         */
        private readonly SaucerInterface $saucer,
        /**
         * Unique window identifier.
         *
         * It is worth noting that the destruction of this object
         * from memory (deallocation using PHP GC) means the physical
         * destruction of all data associated with it, including unmanaged.
         */
        public readonly WindowId $id,
        /**
         * Gets a parent application instance to which this window belongs.
         */
        public readonly Application $app,
        /**
         * Gets information DTO about the window with which it was created.
         */
        public readonly WindowCreateInfo $info,
        EventDispatcherInterface $dispatcher,
    ) {
        // Initialization Window's fields and properties
        $this->listener = new DelegateEventListener($dispatcher);
        $this->webviews = new WebViewManager($saucer, $this, $info->webview, $this->listener);

        // Initialization of Window's API
        $this->extensions = new Registry($this->listener, $info->extensions);
        foreach ($this->extensions->boot($this) as $property => $extension) {
            // Direct access to dynamic property is 5+ times
            // faster than the magic `__get` call.
            $this->__set($property, $extension);
        }

        // Register Window's subsystems
        $this->registerDefaultEventListeners();

        // Boot the Window
        $this->boot();
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
     * Boot the window.
     */
    private function boot(): void
    {
        if ($this->info->visible) {
            $this->show();
        }
    }

    /**
     * Gets current (physical) window title
     */
    private function getCurrentWindowTitle(): string
    {
        $length = $this->saucer->new('size_t');
        $this->saucer->saucer_window_title($this->id->ptr, null, \FFI::addr($length));

        if ($length->cdata === 0) {
            return '';
        }

        $title = $this->saucer->new("char[{$length->cdata}]");
        $this->saucer->saucer_scheme_request_method($this->id->ptr, \FFI::addr($title[0]), \FFI::addr($length));

        return \FFI::string(\FFI::addr($title[0]), $length->cdata);
    }

    /**
     * Registers default event listeners for the window.
     */
    private function registerDefaultEventListeners(): void
    {
        $this->listener->addEventListener(WindowClosed::class, function (): void {
            $this->isClosed = true;

            $this->extensions->destroy();
            $this->webviews->destroy();

            $this->listener->removeAllEventListeners();

            \gc_collect_cycles();
        });

        $this->listener->addEventListener(WindowMinimized::class, function (WindowMinimized $e): void {
            $this->state = $e->isMinimized ? WindowState::Minimized : WindowState::Normal;
        });

        $this->listener->addEventListener(WindowMaximized::class, function (WindowMaximized $e): void {
            $this->state = $e->isMaximized ? WindowState::Maximized : WindowState::Normal;
        });
    }

    /**
     * Magic hack to refresh the window without internal API calls :3
     */
    private function refresh(): void
    {
        $height = $this->size->height;

        // Avoid height overflow
        if ($height >= 2147483647) {
            $this->size->height = $height - 1;
        } else {
            $this->size->height = $height + 1;
        }

        $this->size->height = $height;
    }

    /**
     * Start window dragging.
     *
     * @api
     */
    public function startDrag(): void
    {
        $this->saucer->saucer_window_start_drag($this->id->ptr);
    }

    /**
     * Start window resizing.
     *
     * @api
     */
    public function startResize(WindowEdge|WindowCorner $direction): void
    {
        $this->saucer->saucer_window_start_resize($this->id->ptr, match ($direction) {
            WindowEdge::Top => SaucerWindowEdge::SAUCER_WINDOW_EDGE_TOP,
            WindowEdge::Right => SaucerWindowEdge::SAUCER_WINDOW_EDGE_RIGHT,
            WindowEdge::Bottom => SaucerWindowEdge::SAUCER_WINDOW_EDGE_BOTTOM,
            WindowEdge::Left => SaucerWindowEdge::SAUCER_WINDOW_EDGE_LEFT,
            WindowCorner::TopRight => SaucerWindowEdge::SAUCER_WINDOW_EDGE_TOP
                | SaucerWindowEdge::SAUCER_WINDOW_EDGE_RIGHT,
            WindowCorner::BottomRight => SaucerWindowEdge::SAUCER_WINDOW_EDGE_BOTTOM
                | SaucerWindowEdge::SAUCER_WINDOW_EDGE_RIGHT,
            WindowCorner::BottomLeft => SaucerWindowEdge::SAUCER_WINDOW_EDGE_BOTTOM
                | SaucerWindowEdge::SAUCER_WINDOW_EDGE_LEFT,
            WindowCorner::TopLeft => SaucerWindowEdge::SAUCER_WINDOW_EDGE_TOP
                | SaucerWindowEdge::SAUCER_WINDOW_EDGE_LEFT,
        });
    }

    /**
     * Focus the window.
     *
     * @api
     */
    public function focus(): void
    {
        $this->saucer->saucer_window_focus($this->id->ptr);
    }

    /**
     * Makes this window visible.
     *
     * Note: The same can be done using the window's visibility
     *       property `$window->isVisible = true`.
     *
     * @api
     */
    public function show(): void
    {
        $this->saucer->saucer_window_show($this->id->ptr);
    }

    /**
     * Hides this window.
     *
     * Note: The same can be done using the window's visibility
     *       property `$window->isVisible = false`.
     *
     * @api
     */
    public function hide(): void
    {
        $this->saucer->saucer_window_hide($this->id->ptr);
    }

    /**
     * Set window as maximized.
     *
     * @api
     *
     * @since frontend 0.2.0
     */
    public function maximize(): void
    {
        $this->saucer->saucer_window_set_maximized($this->id->ptr, true);
    }

    /**
     * Set window as maximized.
     *
     * @api
     */
    public function minimize(): void
    {
        $this->saucer->saucer_window_set_minimized($this->id->ptr, true);
    }

    /**
     * Restore window size.
     *
     * @api
     */
    public function restore(): void
    {
        $this->saucer->saucer_window_set_maximized($this->id->ptr, false);
        $this->saucer->saucer_window_set_minimized($this->id->ptr, false);
    }

    /**
     * Closes and destroys this window and its context.
     *
     * @api
     */
    public function close(): void
    {
        if ($this->isClosed) {
            return;
        }

        $this->saucer->saucer_window_close($this->id->ptr);
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
