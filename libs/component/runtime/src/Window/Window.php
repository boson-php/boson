<?php

declare(strict_types=1);

namespace Boson\Window;

use Boson\Application;
use Boson\Dispatcher\DelegateEventListener;
use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Dispatcher\EventListener;
use Boson\Dispatcher\EventListenerInterface;
use Boson\Dispatcher\EventListenerProvider;
use Boson\Dispatcher\EventListenerProviderInterface;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Internal\Saucer\SaucerWindowEdge;
use Boson\Shared\Marker\RequiresDealloc;
use Boson\WebView\Internal\WebViewCreateInfo\FlagsListFormatter;
use Boson\WebView\WebView;
use Boson\Window\Api\WindowExtension;
use Boson\Window\Event\WindowDecorationChanged;
use Boson\Window\Event\WindowMaximized;
use Boson\Window\Event\WindowMinimized;
use Boson\Window\Event\WindowStateChanged;
use Boson\Window\Internal\Size\ManagedWindowMaxBounds;
use Boson\Window\Internal\Size\ManagedWindowMinBounds;
use Boson\Window\Internal\Size\ManagedWindowSize;
use Boson\Window\Internal\SaucerWindowEventHandler;
use Boson\Window\Manager\WindowFactoryInterface;
use FFI\CData;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

/**
 * @api
 */
final class Window implements EventListenerProviderInterface
{
    use EventListenerProvider;

    /**
     * Unique window identifier.
     *
     * It is worth noting that the destruction of this object
     * from memory (deallocation using PHP GC) means the physical
     * destruction of all data associated with it, including unmanaged.
     */
    public readonly WindowId $id;

    /**
     * Gets child webview instance attached to the window.
     */
    public readonly WebView $webview;

    /**
     * Gets access to the listener of the window events
     * and intention subscriptions.
     */
    public readonly EventListenerInterface $events;

    /**
     * Window aware event dispatcher.
     */
    private readonly EventDispatcherInterface $dispatcher;

    /**
     * The title of the specified window encoded as UTF-8.
     */
    public string $title {
        get => $this->title ??= $this->getCurrentWindowTitle();
        set {
            $this->api->saucer_window_set_title($this->id->ptr, $this->title = $value);
        }
    }

    /**
     * Gets window state.
     */
    public private(set) WindowState $state = WindowState::Normal {
        get => $this->state;
        set {
            // Dispatch only if the state has changed
            if ($this->state !== $value) {
                $this->dispatcher->dispatch(new WindowStateChanged(
                    subject: $this,
                    state: $value,
                    previous: $this->state,
                ));
            }

            $this->state = $value;
        }
    }

    /**
     * Provides window decorations configs.
     */
    public WindowDecoration $decoration {
        /**
         * Gets current window decoration value.
         *
         * ```
         * if ($window->decoration === WindowDecoration::DarkMode) {
         *     echo 'Dark mode enabled!;
         * } else {
         *     echo 'Dark mode disabled!';
         * }
         * ```
         */
        get => $this->decoration;
        /**
         * Updates current window decorations mode.
         *
         * ```
         * // Toggle dark mode
         * $window->decoration = $window->decoration === WindowDecoration::DarkMode
         *     ? WindowDecoration::Default
         *     : WindowDecoration::DarkMode;
         * ```
         */
        set {
            /**
             * Skip (just initialize) in case of decoration is uninitialized.
             *
             * @phpstan-ignore-next-line : PHPStan cannot detect uninitialized property state
             */
            if (!isset($this->decoration)) {
                $this->decoration = $value;
                $this->updateDecoration($value);

                return;
            }

            // Do nothing if decoration is equal to previous one.
            if ($value === $this->decoration) {
                return;
            }

            $this->updateDecoration($value);

            $this->dispatcher->dispatch(new WindowDecorationChanged(
                subject: $this,
                decoration: $value,
                previous: $this->decoration,
            ));

            $this->decoration = $value;
        }
    }

    /**
     * Contains current window size.
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
         */
        get => $this->size;
        /**
         * Allows to update window size using any {@see SizeInterface}
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
            /**
             * Allow direct set only on first initialization. First size set
             * MUST be an internal instance of {@see ManagedWindowSize}.
             *
             * @phpstan-ignore-next-line : PHPStan cannot detect uninitialized property state
             */
            if (!isset($this->size)) {
                assert($size instanceof ManagedWindowSize);

                $this->size = $size;

                return;
            }

            $this->size->update($size->width, $size->height);
        }
    }

    /**
     * Contains minimum size bounds of the window.
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
         */
        get => $this->min;
        /**
         * Allows to update window minimal size bound using any
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
            /**
             * Allow direct set only on first initialization. First min size
             * set MUST be an internal instance of {@see ManagedWindowMinBounds}.
             *
             * @phpstan-ignore-next-line : PHPStan cannot detect uninitialized property state
             */
            if (!isset($this->min)) {
                assert($size instanceof ManagedWindowMinBounds);

                $this->min = $size;

                return;
            }

            $this->min->update($size->width, $size->height);
        }
    }

    /**
     * Contains maximum size bounds of the window.
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
         */
        get => $this->max;
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
            /**
             * Allow direct set only on first initialization. First max size
             * set MUST be an internal instance of {@see ManagedWindowMaxBounds}.
             *
             * @phpstan-ignore-next-line : PHPStan cannot detect uninitialized property state
             */
            if (!isset($this->max)) {
                assert($size instanceof ManagedWindowMaxBounds);

                $this->max = $size;

                return;
            }

            $this->max->update($size->width, $size->height);
        }
    }

    /**
     * Contains window visibility option.
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
        get => $this->api->saucer_window_visible($this->id->ptr);
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
                $this->api->saucer_window_show($this->id->ptr);
            } else {
                $this->api->saucer_window_hide($this->id->ptr);
            }
        }
    }

    /**
     * Contains window "always on top" option.
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
        get => $this->api->saucer_window_always_on_top($this->id->ptr);
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
            $this->api->saucer_window_set_always_on_top($this->id->ptr, $value);
        }
    }

    /**
     * Contains window "click through" option.
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
        get => $this->api->saucer_window_click_through($this->id->ptr);
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
            $this->api->saucer_window_set_click_through($this->id->ptr, $value);
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
     */
    public private(set) bool $isClosed = false;

    /**
     * Contains an internal bridge between system {@see LibSaucer} events
     * and the PSR {@see Window::$events} dispatcher.
     *
     * @phpstan-ignore property.onlyWritten
     */
    private readonly SaucerWindowEventHandler $handler;

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
         * Contains shared WebView API library.
         */
        private readonly LibSaucer $api,
        /**
         * Gets parent application instance to which this window belongs.
         */
        public readonly Application $app,
        /**
         * Gets an information DTO about the window with which it was created.
         */
        public readonly WindowCreateInfo $info,
        EventDispatcherInterface $dispatcher,
    ) {
        // Initialization Window's fields and properties
        $this->id = self::createWindowId($api, $app, $this->info);
        $this->events = $this->dispatcher = self::createEventListener($dispatcher);
        $this->size = self::createWindowSize($api, $this->id);
        $this->min = self::createWindowMinSize($api, $this->id);
        $this->max = self::createWindowMaxSize($api, $this->id);
        $this->webview = self::createWebView($api, $this, $info, $this->dispatcher);
        $this->decoration = self::createWindowDecorations($info);
        $this->handler = self::createSaucerWindowEventHandler($api, $this, $this->dispatcher);

        // Initialization of Window's API
        // ...

        // Register Window's subsystems
        $this->registerDefaultEventListeners();

        // Boot the Window
        $this->boot();
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
     * Creates local (window-aware) event listener
     * based on the provided dispatcher.
     */
    private static function createEventListener(PsrEventDispatcherInterface $dispatcher): EventListener
    {
        return new DelegateEventListener($dispatcher);
    }

    /**
     * Creates a new instance of {@see ManagedWindowSize} that wraps the native
     * window size functionality. The returned object allows reading and
     * modifying the window's width and height through a managed interface.
     *
     * The size is managed by the native window system and any changes to the
     * size through this interface will be reflected in the actual
     * window dimensions.
     */
    private static function createWindowSize(LibSaucer $api, WindowId $id): MutableSizeInterface
    {
        return new ManagedWindowSize($api, $id->ptr);
    }

    /**
     * Creates a new instance of {@see ManagedWindowMinBounds} that wraps the
     * native window minimum size bounds functionality. The returned object
     * allows reading and modifying the window's minimum width and height
     * through a managed interface.
     *
     * The minimum size bounds are managed by the native window system and any
     * changes to the bounds through this interface will be reflected in the
     * actual window constraints.
     */
    private static function createWindowMinSize(LibSaucer $api, WindowId $id): MutableSizeInterface
    {
        return new ManagedWindowMinBounds($api, $id->ptr);
    }

    /**
     * Creates a new instance of {@see ManagedWindowMaxBounds} that wraps the
     * native window maximum size bounds functionality. The returned object
     * allows reading and modifying the window's maximum width and height
     * through a managed interface.
     *
     * The maximum size bounds are managed by the native window system and any
     * changes to the bounds through this interface will be reflected in the
     * actual window constraints.
     */
    private static function createWindowMaxSize(LibSaucer $api, WindowId $id): MutableSizeInterface
    {
        return new ManagedWindowMaxBounds($api, $id->ptr);
    }

    /**
     * @template TArgApiProvider of WindowExtension
     *
     * @param class-string<TArgApiProvider> $class
     *
     * @return TArgApiProvider
     *
     * @phpstan-ignore-next-line TODO will be used later
     */
    private function createWindowExtension(string $class): WindowExtension
    {
        return new $class(
            api: $this->api,
            context: $this,
            listener: $this->events,
            dispatcher: $this->dispatcher,
        );
    }

    /**
     * Creates WebView instance of the window.
     *
     * This method initializes and returns a {@see WebView} object
     * that is responsible for managing window's webview.
     */
    private static function createWebView(
        LibSaucer $api,
        Window $window,
        WindowCreateInfo $info,
        EventDispatcherInterface $dispatcher,
    ): WebView {
        return new WebView(
            api: $api,
            window: $window,
            info: $info->webview,
            dispatcher: $dispatcher,
        );
    }

    /**
     * Creates a new instance of {@see SaucerWindowEventHandler} that manages
     * the window's native event handling and bridges them to the Saucer's
     * event system.
     *
     * This method initializes an event handler that translates native window
     * events (like resize, focus, close) into application events that can be
     * handled by the event dispatcher.
     */
    private static function createSaucerWindowEventHandler(
        LibSaucer $api,
        Window $window,
        EventDispatcherInterface $dispatcher,
    ): SaucerWindowEventHandler {
        return new SaucerWindowEventHandler($api, $window, $dispatcher);
    }

    /**
     * Creates an instance of {@see WindowDecoration} based on the window
     * creation information.
     */
    private static function createWindowDecorations(WindowCreateInfo $info): WindowDecoration
    {
        return $info->decoration;
    }

    /**
     * Creates new window ID and internal handle
     */
    private static function createWindowId(LibSaucer $api, Application $app, WindowCreateInfo $info): WindowId
    {
        return WindowId::fromHandle(
            api: $api,
            handle: self::createWindowPointer($api, $app, $info),
        );
    }

    /**
     * Gets current (physical) window title
     */
    private function getCurrentWindowTitle(): string
    {
        $result = $this->api->saucer_window_title($this->id->ptr);

        try {
            return \FFI::string($result);
        } finally {
            \FFI::free($result);
        }
    }

    #[RequiresDealloc]
    private static function createWindowPointer(LibSaucer $api, Application $app, WindowCreateInfo $info): CData
    {
        $preferences = self::createPreferencesPointer($api, $app, $info);

        // Enable dev tools in case of the corresponding value was passed
        // explicitly to the create info options or debug mode was enabled.
        $isDevToolsEnabled = $info->webview->devTools ?? $app->isDebug;

        // Enable context menu in case of the corresponding value was passed
        // explicitly to the create info options or debug mode was enabled.
        $isContextMenuEnabled = $info->webview->contextMenu ?? $app->isDebug;

        if ($isDevToolsEnabled) {
            /**
             * Force disable unnecessary XSS warnings in dev tools
             *
             * @link https://developer.chrome.com/blog/self-xss#can_you_disable_it_for_test_automation
             */
            $api->saucer_preferences_add_browser_flag(
                $preferences,
                '--unsafely-disable-devtools-self-xss-warnings',
            );
        }

        try {
            $handle = $api->saucer_new($preferences);

            if ($info->decoration === WindowDecoration::DarkMode) {
                $api->saucer_webview_set_force_dark_mode($handle, true);
            }

            if ($info->title !== '') {
                $api->saucer_window_set_title($handle, $info->title);
            }

            if ($info->resizable === false) {
                $api->saucer_window_set_resizable($handle, false);
            }

            if ($info->alwaysOnTop === true) {
                $api->saucer_window_set_always_on_top($handle, true);
            }

            if ($info->clickThrough === true) {
                $api->saucer_window_set_click_through($handle, true);
            }

            $api->saucer_window_set_size($handle, $info->width, $info->height);
            $api->saucer_webview_set_context_menu($handle, $isContextMenuEnabled);
            $api->saucer_webview_set_dev_tools($handle, $isDevToolsEnabled);

            return $handle;
        } finally {
            $api->saucer_preferences_free($preferences);
        }
    }

    #[RequiresDealloc]
    private static function createPreferencesPointer(LibSaucer $api, Application $app, WindowCreateInfo $info): CData
    {
        $preferences = $api->saucer_preferences_new($app->id->ptr);

        // Hardware acceleration is enabled by default.
        if ($info->enableHardwareAcceleration === false) {
            $api->saucer_preferences_set_hardware_acceleration($preferences, false);
        }

        // The "persistent cookies" feature uses storage value.
        // If this functionality is not required,
        // then storage can be omitted.
        if ($info->webview->storage === false) {
            $api->saucer_preferences_set_persistent_cookies($preferences, false);
        } else {
            $api->saucer_preferences_set_storage_path($preferences, $info->webview->storage);
        }

        // Specify additional flags using the formatter.
        foreach (FlagsListFormatter::format($info->webview->flags) as $value) {
            $api->saucer_preferences_add_browser_flag($preferences, $value);
        }

        // Define the "user-agent" header if it is specified.
        if ($info->webview->userAgent !== null) {
            $api->saucer_preferences_set_user_agent($preferences, $info->webview->userAgent);
        }

        return $preferences;
    }

    /**
     * Registers default event listeners for the window.
     */
    private function registerDefaultEventListeners(): void
    {
        $this->events->addEventListener(WindowMinimized::class, function (WindowMinimized $e): void {
            $this->state = $e->isMinimized ? WindowState::Minimized : WindowState::Normal;
        });

        $this->events->addEventListener(WindowMaximized::class, function (WindowMaximized $e): void {
            $this->state = $e->isMaximized ? WindowState::Maximized : WindowState::Normal;
        });
    }

    /**
     * Apply selected decoration to the window styles.
     */
    private function updateDecoration(WindowDecoration $decoration): void
    {
        $ptr = $this->id->ptr;

        $this->api->saucer_webview_background(
            $ptr,
            \FFI::addr($r = $this->api->new('uint8_t')),
            \FFI::addr($g = $this->api->new('uint8_t')),
            \FFI::addr($b = $this->api->new('uint8_t')),
            \FFI::addr($a = $this->api->new('uint8_t')),
        );

        $isDarkModeWasEnabled = $this->api->saucer_webview_force_dark_mode($ptr);

        // Please note that the order of function calls is important,
        // as there is a bug in the kernel of saucer v6.0 that causes
        // loss of state after change decorations.
        //
        // ```
        // if (!decorated) {
        //     impl::set_style(m_impl->hwnd.get(), 0); // previous WS_XXX
        //                                             // state has been lost?
        //     return;
        // }
        // ```
        //
        // We need to figure it out...
        switch ($decoration) {
            case WindowDecoration::DarkMode:
                if ($a->cdata !== 255) {
                    /** @phpstan-ignore-next-line : PHPStan does not support FFI correctly */
                    $this->api->saucer_webview_set_background($ptr, $r->cdata, $g->cdata, $b->cdata, 255);
                }

                $this->api->saucer_window_set_decorations($ptr, true);

                // Refresh in case of dark mode was disabled
                if ($isDarkModeWasEnabled === false) {
                    $this->api->saucer_webview_set_force_dark_mode($ptr, true);
                    $this->refresh();
                }
                break;

            case WindowDecoration::Frameless:
                if ($a->cdata !== 255) {
                    /** @phpstan-ignore-next-line : PHPStan does not support FFI correctly */
                    $this->api->saucer_webview_set_background($ptr, $r->cdata, $g->cdata, $b->cdata, 255);
                }

                $this->api->saucer_window_set_decorations($ptr, false);
                break;

            case WindowDecoration::Transparent:
                $this->api->saucer_window_set_decorations($ptr, false);

                if ($a->cdata !== 0) {
                    /** @phpstan-ignore-next-line : PHPStan does not support FFI correctly */
                    $this->api->saucer_webview_set_background($ptr, $r->cdata, $g->cdata, $b->cdata, 0);
                }
                break;

            default:
                if ($a->cdata !== 255) {
                    /** @phpstan-ignore-next-line : PHPStan does not support FFI correctly */
                    $this->api->saucer_webview_set_background($ptr, $r->cdata, $g->cdata, $b->cdata, 255);
                }

                $this->api->saucer_window_set_decorations($ptr, true);

                // Refresh in case of dark mode was enabled
                if ($isDarkModeWasEnabled) {
                    $this->api->saucer_webview_set_force_dark_mode($ptr, false);
                    $this->refresh();
                }
        }
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
        $this->api->saucer_window_start_drag($this->id->ptr);
    }

    /**
     * Start window resizing.
     *
     * @api
     */
    public function startResize(WindowEdge|WindowCorner $direction): void
    {
        $this->api->saucer_window_start_resize($this->id->ptr, match ($direction) {
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
        $this->api->saucer_window_focus($this->id->ptr);
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
        $this->api->saucer_window_show($this->id->ptr);
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
        $this->api->saucer_window_hide($this->id->ptr);
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
        $this->api->saucer_window_set_maximized($this->id->ptr, true);
    }

    /**
     * Set window as maximized.
     *
     * @api
     */
    public function minimize(): void
    {
        $this->api->saucer_window_set_minimized($this->id->ptr, true);
    }

    /**
     * Restore window size.
     *
     * @api
     */
    public function restore(): void
    {
        $this->api->saucer_window_set_maximized($this->id->ptr, false);
        $this->api->saucer_window_set_minimized($this->id->ptr, false);
    }

    /**
     * Closes and destroys this window and its context.
     *
     * @api
     */
    public function close(): void
    {
        $this->isClosed = true;
        $this->api->saucer_window_close($this->id->ptr);
    }

    public function __destruct()
    {
        $this->isClosed = true;
    }
}
