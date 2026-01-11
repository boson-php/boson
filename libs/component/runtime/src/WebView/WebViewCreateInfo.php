<?php

declare(strict_types=1);

namespace Boson\WebView;

use Boson\Application;
use Boson\Extension\ExtensionInterface;
use Boson\WebView\Api\Bindings\BindingsExtension;
use Boson\WebView\Api\Data\DataExtension;
use Boson\WebView\Api\LifecycleEvents\LifecycleEventsExtension;
use Boson\WebView\Api\Schemes\SchemesExtension;
use Boson\WebView\Api\Scripts\ScriptsExtension;
use Boson\WebView\Api\Security\SecurityExtension;
use Boson\WebView\WebViewCreateInfo\StorageDirectoryResolver;
use Boson\Window\WindowCreateInfo;

//
// Note:
// 1) This "$_" assign hack removes these constants from IDE autocomplete.
// 2) Only define-like constants allows object instances.
//
\define($_ = 'Boson\WebView\DEFAULT_WEBVIEW_EXTENSIONS', [
    new ScriptsExtension(),
    new BindingsExtension(),
    new DataExtension(),
    new SecurityExtension(),
    new SchemesExtension(),
    new LifecycleEventsExtension(),
]);

/**
 * Information (configuration) about creating a new webview object.
 */
final readonly class WebViewCreateInfo
{
    /**
     * @var list<ExtensionInterface<WebView>>
     *
     * @noinspection PhpUndefinedConstantInspection
     */
    public const array DEFAULT_WEBVIEW_EXTENSIONS = DEFAULT_WEBVIEW_EXTENSIONS;

    /**
     * Path to directory with temporary files (WebView configuration
     * and session) files.
     *
     * In case of {@see false} the storage (and any session data) will be
     * disabled.
     *
     * Note: [MACOS] May behave unexpected on MacOS. WKWebView does not
     *       offer any way to store webview data to a specific file,
     *       instead it associates the data with a UUID through some
     *       opaque means. Thus a UUID derived from the provided
     *       storage path given to WKWebView.
     *
     * @var non-empty-string|false
     */
    public string|false $storage;

    /**
     * Gets original of additional platform-dependent webview flags (options).
     *
     * Note: [WINDOWS] For WebView2 a list of all available flags can be
     *       seen here {@link https://learn.microsoft.com/en-us/dotnet/api/microsoft.web.webview2.core.corewebview2environmentoptions.additionalbrowserarguments}
     *       and here {@link https://www.chromium.org/developers/how-tos/run-chromium-with-flags/}
     *       ```
     *       new WebViewCreateInfo(flags: [
     *           '--disable-features' => ['feature1', 'feature2'],
     *           '--do-something',
     *       ]);
     *       ```
     *
     * Note: [LINUX] For WebkitGTK a list of all available flags can be
     *       seen here {@link https://webkitgtk.org/reference/webkitgtk/stable/class.Settings.html#properties}
     *       ```
     *       new WebViewCreateInfo(flags: [
     *           'enable-javascript' => false,
     *       ]);
     *       ```
     *
     * Note: [MACOS] For WKWebView a list of all available flags can be
     *       seen here {@link https://developer.apple.com/documentation/webkit/wkwebviewconfiguration}
     *       ```
     *       new WebViewCreateInfo(flags: [
     *           'upgradeKnownHostsToHTTPS' => true,
     *           'defaultWebpagePreferences.allowsContentJavaScript' => false,
     *           'preferences.minimumFontSize' => 10,
     *           'applicationNameForUserAgent' => 'Boson',
     *       ]);
     *       ```
     *
     * @var array<non-empty-string, string|float|bool|int|list<string|float|bool|int>>
     */
    public array $flags;

    /**
     * @var list<ExtensionInterface<WebView>>
     */
    public array $extensions;

    /**
     * @param non-empty-string|null $storage See {@see WebViewCreateInfo::$storage}
     *        field description.
     *
     *        - In case of {@see null} the default value will be set to
     *         {@see getcwd()} CWD and
     *         {@see StorageDirectoryResolver::DEFAULT_STORAGE_DIRECTORY_NAME}
     *         constant value.
     *
     *        - In case of {@see false} the storage (and any session persistent
     *          data) will be disabled.
     * @param iterable<non-empty-string, string|float|bool|int|list<string|float|bool|int>> $flags
     *        See the {@see $flags} property description for information
     * @param iterable<mixed, ExtensionInterface<WebView>> $extensions
     *         list of enabled webview extensions
     */
    public function __construct(
        /**
         * This option may be set to customize "user-agent" browser header.
         *
         * @link https://developer.mozilla.org/ru/docs/Web/HTTP/Reference/Headers/User-Agent
         *
         * @var non-empty-string|null
         */
        public ?string $userAgent = null,
        string|false|null $storage = false,
        iterable $flags = [],
        /**
         * Enable or disable default webview context menu (right mouse button).
         *
         * If {@see null} is passed (by default), the behavior depends
         * on the application's debug ({@see Application::$isDebug}) settings:
         *  - Context menu will be enabled if debug mode is enabled.
         *  - Context menu will bew disabled if debug mode is disabled.
         */
        public ?bool $contextMenu = null,
        /**
         * Enable or disable default webview dev tools (F12 key).
         *
         * If {@see null} is passed (by default), the behavior depends
         * on the application's debug ({@see Application::$isDebug}) settings:
         *  - Dev Tools will be enabled if debug mode is enabled.
         *  - Dev Tools will bew disabled if debug mode is disabled.
         */
        public ?bool $devTools = null,
        /**
         * Enables graphics hardware acceleration in case of this option
         * is set to {@see true} or disables in case {@see false}.
         *
         * By default, in case of {@see null}, uses parent (windows)
         * configuration option:
         * - {@see WindowCreateInfo::$enableHardwareAcceleration}
         *
         * Note: [MACOS] WKWebView does not allow to control
         *       hardware-acceleration.
         */
        public ?bool $enableHardwareAcceleration = null,
        /**
         * Forces dark mode in case of this option
         * is set to {@see true} or disables in case {@see false}.
         *
         * By default, in case of {@see null}, uses parent (windows)
         * configuration option:
         * - {@see WindowCreateInfo::$decoration}
         */
        public ?bool $forceDarkMode = null,
        iterable $extensions = self::DEFAULT_WEBVIEW_EXTENSIONS,
    ) {
        $this->storage = StorageDirectoryResolver::resolve($storage);
        $this->flags = self::flagsToList($flags);
        $this->extensions = self::extensionsToList($extensions);
    }

    /**
     * @param iterable<non-empty-string, string|float|bool|int|list<string|float|bool|int>> $flags
     *
     * @return array<non-empty-string, string|float|bool|int|list<string|float|bool|int>>
     */
    private static function flagsToList(iterable $flags): array
    {
        return \iterator_to_array($flags, true);
    }

    /**
     * @param iterable<mixed, ExtensionInterface<WebView>> $extensions
     *
     * @return list<ExtensionInterface<WebView>>
     */
    private static function extensionsToList(iterable $extensions): array
    {
        return \iterator_to_array($extensions, false);
    }

    /**
     * @param list<ExtensionInterface<WebView>> $with
     * @param list<class-string<ExtensionInterface<WebView>>> $except
     *
     * @return iterable<array-key, ExtensionInterface<WebView>>
     */
    public static function extensions(array $with = [], array $except = []): iterable
    {
        /**
         * @var ExtensionInterface<WebView> $extension
         *
         * @phpstan-ignore-next-line PHPStan does not support this constant
         */
        foreach (self::DEFAULT_WEBVIEW_EXTENSIONS as $extension) {
            if (\in_array($extension::class, $except, true)) {
                continue;
            }

            yield $extension;
        }

        yield from $with;
    }
}
