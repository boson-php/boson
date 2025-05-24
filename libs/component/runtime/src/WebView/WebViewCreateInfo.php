<?php

declare(strict_types=1);

namespace Boson\WebView;

use Boson\Application;
use Boson\WebView\Api\BindingsApiCreateInfo;
use Boson\WebView\Api\DataApiCreateInfo;
use Boson\WebView\Api\WebComponentsCreateInfo;
use Boson\WebView\Internal\WebViewCreateInfo\StorageDirectoryResolver;

/**
 * Information (configuration) about creating a new webview object.
 */
final readonly class WebViewCreateInfo
{
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
     * List of scripts which will always be loaded on any page
     * (executed after DOM has been ready).
     *
     * @var list<string>
     */
    public array $scripts;

    /**
     * @param iterable<mixed, string> $scripts see the {@see $scripts} property
     *        description for information
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
     */
    public function __construct(
        /**
         * An URL/URI that should be loaded when creating a webview.
         *
         * Note: You can specify either {@see $url} OR {@see $html},
         *       but NOT both.
         *
         * @var non-empty-string|null
         */
        public ?string $url = null,
        /**
         * HTML content that should be loaded when creating a webview.
         *
         * Note: You can specify either {@see $url} OR {@see $html},
         *       but NOT both.
         *
         * @var non-empty-string|null
         */
        public ?string $html = null,
        iterable $scripts = [],
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
         * Contains Bindings API configuration options.
         */
        public BindingsApiCreateInfo $bindings = new BindingsApiCreateInfo(),
        /**
         * Contains Data API configuration options.
         */
        public DataApiCreateInfo $data = new DataApiCreateInfo(),
        /**
         * Contains Web Components API configuration options.
         */
        public WebComponentsCreateInfo $webComponents = new WebComponentsCreateInfo(),
    ) {
        assert($url === null || $html === null, new \InvalidArgumentException(
            message: 'You can specify either $url or $html, but not both',
        ));

        $this->storage = StorageDirectoryResolver::resolve($storage);
        $this->flags = \iterator_to_array($flags, true);
        $this->scripts = \iterator_to_array($scripts, false);
    }
}
