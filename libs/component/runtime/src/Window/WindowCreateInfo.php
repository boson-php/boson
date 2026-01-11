<?php

declare(strict_types=1);

namespace Boson\Window;

use Boson\Extension\ExtensionInterface;
use Boson\WebView\WebViewCreateInfo;
use Boson\Window\Api\LifecycleEvents\LifecycleEventsExtension;

//
// Note:
// 1) This "$_" assign hack removes these constants from IDE autocomplete.
// 2) Only define-like constants allows object instances.
//
\define($_ = 'Boson\Window\DEFAULT_WINDOW_EXTENSIONS', [
    new LifecycleEventsExtension(),
]);

/**
 * Information (configuration) DTO for creating a new physical window.
 */
final readonly class WindowCreateInfo
{
    /**
     * @var list<ExtensionInterface<Window>>
     *
     * @noinspection PhpUndefinedConstantInspection
     */
    public const array DEFAULT_WINDOW_EXTENSIONS = DEFAULT_WINDOW_EXTENSIONS;

    /**
     * Gets default window width.
     */
    public const int DEFAULT_WIDTH = 640;

    /**
     * Gets default window height.
     */
    public const int DEFAULT_HEIGHT = 480;

    /**
     * @var list<ExtensionInterface<Window>>
     */
    public array $extensions;

    /**
     * @param iterable<mixed, ExtensionInterface<Window>> $extensions
     *        list of enabled window extensions
     */
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
         * Enables hardware acceleration for all children webview instances.
         *
         * @see WebViewCreateInfo::$enableHardwareAcceleration
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
         * Sets the mode for disabling mouse event capture.
         */
        public bool $clickThrough = false,
        /**
         * Manage window decorations.
         *
         * Enable or disable title bar, minimize, maximize, exit buttons,
         * transparency and so on...
         */
        public WindowDecoration $decoration = WindowDecoration::Default,
        iterable $extensions = self::DEFAULT_WINDOW_EXTENSIONS,
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

        $this->extensions = self::extensionsToList($extensions);
    }

    /**
     * @param iterable<mixed, ExtensionInterface<Window>> $extensions
     *
     * @return list<ExtensionInterface<Window>>
     */
    private static function extensionsToList(iterable $extensions): array
    {
        return \iterator_to_array($extensions, false);
    }

    /**
     * @param list<ExtensionInterface<Window>> $with
     * @param list<class-string<ExtensionInterface<Window>>> $except
     *
     * @return iterable<array-key, ExtensionInterface<Window>>
     */
    public static function extensions(array $with = [], array $except = []): iterable
    {
        /**
         * @var ExtensionInterface<Window> $extension
         *
         * @phpstan-ignore-next-line PHPStan does not support this constant
         */
        foreach (self::DEFAULT_WINDOW_EXTENSIONS as $extension) {
            if (\in_array($extension::class, $except, true)) {
                continue;
            }

            yield $extension;
        }

        yield from $with;
    }
}
