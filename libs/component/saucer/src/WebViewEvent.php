<?php

declare(strict_types=1);

namespace Boson\Component\Saucer;

final readonly class WebViewEvent
{
    /**
     * Emitted when a web-page requests a permission.
     */
    public const int SAUCER_WEBVIEW_EVENT_PERMISSION = 0;

    /**
     * Emitted when a web-page tries to go into fullscreen.
     */
    public const int SAUCER_WEBVIEW_EVENT_FULLSCREEN = 1;

    /**
     * Called when the DOM Content loaded.
     */
    public const int SAUCER_WEBVIEW_EVENT_DOM_READY = 2;

    /**
     * Called when a new URL was loaded.
     */
    public const int SAUCER_WEBVIEW_EVENT_NAVIGATED = 3;

    /**
     * Called when the URL is about to change.
     */
    public const int SAUCER_WEBVIEW_EVENT_NAVIGATE = 4;

    /**
     * Emitted when an RPC message is sent.
     */
    public const int SAUCER_WEBVIEW_EVENT_MESSAGE = 5;

    /**
     * Emitted when a web-resource is requested.
     *
     * Note: Please note that this event behaves differently among various
     *       backends. For example, on WebKitGtk, this event will be fired
     *       less frequently in comparison to e.g. WebView2.
     */
    public const int SAUCER_WEBVIEW_EVENT_REQUEST = 6;

    /**
     * Called when the favicon changes.
     */
    public const int SAUCER_WEBVIEW_EVENT_FAVICON = 7;

    /**
     * Called when the document title changes.
     */
    public const int SAUCER_WEBVIEW_EVENT_TITLE = 8;

    /**
     * Called when the web-page load progresses.
     */
    public const int SAUCER_WEBVIEW_EVENT_LOAD = 9;

    private function __construct() {}
}
