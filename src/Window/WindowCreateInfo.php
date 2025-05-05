<?php

declare(strict_types=1);

namespace Boson\Window;

use Boson\WebView\WebViewCreateInfo;

/**
 * Information (configuration) DTO for creating a new physical window.
 */
final readonly class WindowCreateInfo
{
    /**
     * Gets default window width.
     */
    public const int DEFAULT_WIDTH = 640;

    /**
     * Gets default window height.
     */
    public const int DEFAULT_HEIGHT = 480;

    public function __construct(
        /**
         * Sets initial window title.
         */
        public string $title = '',
        /**
         * Sets initial window width.
         *
         * @var int<0, 2147483647>
         */
        public int $width = self::DEFAULT_WIDTH,
        /**
         * Sets initial window height.
         *
         * @var int<0, 2147483647>
         */
        public int $height = self::DEFAULT_HEIGHT,
        /**
         * Enables graphics hardware acceleration in case of this option
         * is set to {@see true} or disables in case {@see false}.
         *
         * Note: [MACOS] WKWebView does not allow to control
         *       hardware-acceleration.
         */
        public bool $enableHardwareAcceleration = true,
        /**
         * Displays a window when the application starts.
         */
        public bool $visible = true,
        /**
         * Allows the user to resize the window.
         */
        public bool $resizable = true,
        /**
         * Sets the window to always be on top.
         */
        public bool $alwaysOnTop = false,
        /**
         * Manage window decorations.
         *
         * Enable or disable title bar, minimize, maximize, exit buttons,
         * transparency and so on...
         */
        public WindowDecoration $decoration = WindowDecoration::Default,
        /**
         * Information (configuration) about creating a new webview object
         * that will be attached to the window.
         */
        public WebViewCreateInfo $webview = new WebViewCreateInfo(),
    ) {
        assert($width >= 0 && $width <= 2147483647, new \InvalidArgumentException(
            message: 'Window width CAN NOT be less than 0 or greater than 2147483647',
        ));

        assert($height >= 0 && $height <= 2147483647, new \InvalidArgumentException(
            message: 'Window height CAN NOT be less than 0 or greater than 2147483647',
        ));
    }
}
