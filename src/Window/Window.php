<?php

declare(strict_types=1);

namespace Boson\Window;

use Boson\Application;
use Boson\Dispatcher\DelegateEventListener;
use Boson\Dispatcher\EventListener;
use Boson\Http\Method\MethodFactoryInterface;
use Boson\Http\Uri\Factory\UriFactoryInterface;
use Boson\Internal\ProcessUnlockPlaceholder;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Shared\Marker\RequiresDealloc;
use Boson\WebView\Internal\WebViewCreateInfo\FlagsListFormatter;
use Boson\WebView\WebView;
use Boson\Window\Internal\Size\ManagedWindowMaxBounds;
use Boson\Window\Internal\Size\ManagedWindowMinBounds;
use Boson\Window\Internal\Size\ManagedWindowSize;
use Boson\Window\Internal\WindowEventHandler;
use Boson\Window\Manager\WindowFactoryInterface;
use FFI\CData;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @api
 */
final class Window
{
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
    public readonly EventListener $events;

    /**
     * The title of the specified window encoded as UTF-8.
     */
    public string $title {
        get => $this->title ??= $this->getCurrentWindowTitle();
        set {
            $this->api->saucer_window_set_title($this->id->ptr, $this->title = $value);
        }
    }

    public WindowDecoration $decoration {
        get => $this->decoration;
        set {
            // Do nothing if decoration is equal to previous one
            /** @phpstan-ignore-next-line : PHPStan false positive */
            if (isset($this->decoration) && $value === $this->decoration) {
                return;
            }

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
            switch ($this->decoration = $value) {
                case WindowDecoration::DarkMode:
                    /** @phpstan-ignore-next-line : PHPStan does not support FFI correctly */
                    $this->api->saucer_webview_set_background($ptr, $r->cdata, $g->cdata, $b->cdata, 255);
                    $this->api->saucer_window_set_decorations($ptr, true);

                    // Refresh in case of dark mode was disabled
                    if ($isDarkModeWasEnabled === false) {
                        $this->api->saucer_webview_set_force_dark_mode($ptr, true);
                        $this->refresh();
                    }
                    break;

                case WindowDecoration::Frameless:
                    /** @phpstan-ignore-next-line : PHPStan does not support FFI correctly */
                    $this->api->saucer_webview_set_background($ptr, $r->cdata, $g->cdata, $b->cdata, 255);
                    $this->api->saucer_window_set_decorations($ptr, false);
                    break;

                case WindowDecoration::Transparent:
                    $this->api->saucer_window_set_decorations($ptr, false);
                    /** @phpstan-ignore-next-line : PHPStan does not support FFI correctly */
                    $this->api->saucer_webview_set_background($ptr, $r->cdata, $g->cdata, $b->cdata, 0);
                    break;

                default:
                    /** @phpstan-ignore-next-line : PHPStan does not support FFI correctly */
                    $this->api->saucer_webview_set_background($ptr, $r->cdata, $g->cdata, $b->cdata, 255);
                    $this->api->saucer_window_set_decorations($ptr, true);

                    // Refresh in case of dark mode was enabled
                    if ($isDarkModeWasEnabled) {
                        $this->api->saucer_webview_set_force_dark_mode($ptr, false);
                        $this->refresh();
                    }
            }
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
            if ($size instanceof ManagedWindowSize) {
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
            if ($size instanceof ManagedWindowMinBounds) {
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
            if ($size instanceof ManagedWindowMaxBounds) {
                $this->max = $size;

                return;
            }

            $this->max->update($size->width, $size->height);
        }
    }

    /**
     * Contains window dark mode option.
     *
     * In case of {@see true} then the dark mode is forcibly enabled,
     * or {@see false} instead.
     */
    public bool $isDarkModeEnabled {
        /**
         * Gets current window dark mode.
         *
         * ```
         * if ($window->isDarkModeEnabled) {
         *     echo 'Dark mode is enabled';
         * } else {
         *     echo 'Dark mode is disabled';
         * }
         * ```
         */
        get {
            return $this->isDarkModeEnabled
                ??= $this->api->saucer_webview_force_dark_mode($this->id->ptr);
        }
        /**
         * Updates current window dark mode.
         *
         * ```
         * $window->isDarkModeEnabled = true;
         * ```
         */
        set {
            $this->api->saucer_webview_set_force_dark_mode(
                $this->id->ptr,
                $this->isDarkModeEnabled = $value,
            );
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
    private readonly WindowEventHandler $handler;

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
         * Contains an internal application placeholder to unlock the
         * webview process workflow.
         */
        private readonly ProcessUnlockPlaceholder $placeholder,
        /**
         * Contains factory to create URI instances.
         */
        private readonly UriFactoryInterface $uris,
        /**
         * Contains factory to create HTTP method instances.
         */
        private readonly MethodFactoryInterface $methods,
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
        $this->id = $this->createWindowId($this->info);

        $this->events = new DelegateEventListener($dispatcher);
        $this->size = new ManagedWindowSize($this->api, $this->id->ptr);
        $this->min = new ManagedWindowMinBounds($this->api, $this->id->ptr);
        $this->max = new ManagedWindowMaxBounds($this->api, $this->id->ptr);
        $this->webview = $this->createWebView();

        $this->handler = new WindowEventHandler(
            api: $this->api,
            window: $this,
            dispatcher: $this->events,
        );

        $this->decoration = $this->info->decoration;

        if ($this->info->visible) {
            $this->show();
        }
    }

    /**
     * Creates WebView instance of the window
     */
    private function createWebView(): WebView
    {
        return new WebView(
            api: $this->api,
            placeholder: $this->placeholder,
            uris: $this->uris,
            methods: $this->methods,
            window: $this,
            info: $this->info->webview,
            dispatcher: $this->events,
        );
    }

    /**
     * Creates new window ID and internal handle
     */
    private function createWindowId(WindowCreateInfo $info): WindowId
    {
        return WindowId::fromHandle(
            api: $this->api,
            handle: $this->createWindowPointer($info),
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
    private function createWindowPointer(WindowCreateInfo $info): CData
    {
        $preferences = $this->createPreferencesPointer($info);

        try {
            $handle = $this->api->saucer_new($preferences);

            if ($info->decoration === WindowDecoration::DarkMode) {
                $this->api->saucer_webview_set_force_dark_mode($handle, true);
            }

            if ($info->title !== '') {
                $this->api->saucer_window_set_title($handle, $info->title);
            }

            if ($info->resizable === false) {
                $this->api->saucer_window_set_resizable($handle, false);
            }

            $this->api->saucer_window_set_size($handle, $info->width, $info->height);

            // Enable context menu in case of the corresponding value was passed
            // explicitly to the create info options or debug mode was enabled.
            $isContextMenuEnabled = $info->webview->contextMenu ?? $this->app->isDebug;
            $this->api->saucer_webview_set_context_menu($handle, $isContextMenuEnabled);

            // Enable dev tools in case of the corresponding value was passed
            // explicitly to the create info options or debug mode was enabled.
            $isDevToolsEnabled = $info->webview->devTools ?? $this->app->isDebug;
            $this->api->saucer_webview_set_dev_tools($handle, $isDevToolsEnabled);

            return $handle;
        } finally {
            $this->api->saucer_preferences_free($preferences);
        }
    }

    #[RequiresDealloc]
    private function createPreferencesPointer(WindowCreateInfo $info): CData
    {
        $preferences = $this->api->saucer_preferences_new($this->app->id->ptr);

        // Hardware acceleration is enabled by default.
        if ($info->enableHardwareAcceleration === false) {
            $this->api->saucer_preferences_set_hardware_acceleration($preferences, false);
        }

        // The "persistent cookies" feature uses storage value.
        // If this functionality is not required,
        // then storage can be omitted.
        if ($info->webview->storage === false) {
            $this->api->saucer_preferences_set_persistent_cookies($preferences, false);
        } else {
            $this->api->saucer_preferences_set_storage_path($preferences, $info->webview->storage);
        }

        // Specify additional flags using the formatter.
        foreach (FlagsListFormatter::format($info->webview->flags) as $value) {
            $this->api->saucer_preferences_add_browser_flag($preferences, $value);
        }

        // Define the "user-agent" header if it is specified.
        if ($info->webview->userAgent !== null) {
            $this->api->saucer_preferences_set_user_agent($preferences, $info->webview->userAgent);
        }

        return $preferences;
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
     * @internal Does not works correctly, awaiting for {@link https://github.com/saucer/bindings/pull/4} merge
     * @api
     */
    public function maximize(): void
    {
        $this->api->saucer_window_set_minimized($this->id->ptr, false);
        $this->api->saucer_window_set_maximized($this->id->ptr, true);
    }

    /**
     * Set window as maximized.
     *
     * @api
     */
    public function minimize(): void
    {
        $this->api->saucer_window_set_maximized($this->id->ptr, false);
        $this->api->saucer_window_set_minimized($this->id->ptr, true);
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
