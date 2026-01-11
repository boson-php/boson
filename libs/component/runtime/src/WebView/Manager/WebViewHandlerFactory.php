<?php

declare(strict_types=1);

namespace Boson\WebView\Manager;

use Boson\Application;
use Boson\Component\Saucer\SaucerInterface;
use Boson\Shared\Marker\RequiresDealloc;
use Boson\WebView\Exception\WebViewException;
use Boson\WebView\WebViewCreateInfo;
use Boson\WebView\WebViewCreateInfo\FlagsListFormatter;
use Boson\WebView\WebViewId;
use Boson\Window\Window;
use Boson\Window\WindowDecoration;
use FFI\CData;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Manager
 */
final readonly class WebViewHandlerFactory
{
    public function __construct(
        private SaucerInterface $saucer,
    ) {}

    public function create(Window $window, WebViewCreateInfo $info): WebViewId
    {
        return WebViewId::fromHandle(
            api: $this->saucer,
            handle: $this->createWebViewHandle($window, $info),
        );
    }

    #[RequiresDealloc]
    private function createWebViewHandle(Window $window, WebViewCreateInfo $info): CData
    {
        $options = $this->saucer->saucer_webview_options_new($window->id->ptr);

        $handle = $this->createWebViewHandleWithOptions($options, $window, $info);

        $this->saucer->saucer_webview_options_free($options);

        return $handle;
    }

    #[RequiresDealloc]
    private function createWebViewHandleWithOptions(CData $options, Window $window, WebViewCreateInfo $info): CData
    {
        $this->applyWebViewBeforeCreated($options, $window, $info);

        $handle = $this->saucer->saucer_webview_new($options, \FFI::addr(
            $error = $this->saucer->new('int'),
        ));

        if ($error->cdata !== 0) {
            throw new WebViewException('An error occurred while creating WebView', $error->cdata);
        }

        $this->applyWebViewAfterCreated($handle, $window, $info);

        return $handle;
    }

    /**
     * Enable dev tools in case of the corresponding value was passed
     * explicitly to the create info options or debug mode was enabled.
     */
    private function isDevToolsEnabled(Window $window, WebViewCreateInfo $info): bool
    {
        return $info->webview->devTools
            ?? $window->app->isDebug;
    }

    /**
     * Enable context menu in case of the corresponding value was passed
     * explicitly to the create info options or debug mode was enabled.
     */
    private function isContextMenuEnabled(Window $window, WebViewCreateInfo $info): bool
    {
        return $info->webview->contextMenu
            ?? $window->app->isDebug;
    }

    /**
     * Enable dark mode in case of the corresponding value was passed in
     * parent window config instance.
     *
     * TODO Move this option to webview
     */
    private function isDarkModeEnabled(Window $window,WebViewCreateInfo $info): bool
    {
        return $info->forceDarkMode
            ?? $window->info->decoration === WindowDecoration::DarkMode;
    }

    /**
     * Gets real hardware acceleration option from configuration options
     */
    private function isHardwareAccelerationEnabled(Window $window,WebViewCreateInfo $info): bool
    {
        return $info->enableHardwareAcceleration
            ?? $window->info->enableHardwareAcceleration;
    }

    private function applyWebViewBeforeCreated(CData $options, Window $window, WebViewCreateInfo $info): void
    {
        if ($this->isDevToolsEnabled($window, $info)) {
            /**
             * Force disable unnecessary XSS warnings in dev tools
             *
             * @link https://developer.chrome.com/blog/self-xss#can_you_disable_it_for_test_automation
             */
            $this->saucer->saucer_webview_options_append_browser_flag(
                $options,
                '--unsafely-disable-devtools-self-xss-warnings',
            );
        }

        // The "persistent cookies" feature uses storage value.
        // If this functionality is not required, then storage can be omitted.
        if ($info->storage === false) {
            $this->saucer->saucer_webview_options_set_persistent_cookies($options, false);
        } else {
            $this->saucer->saucer_webview_options_set_storage_path($options, $info->storage);
        }

        // Specify additional flags using the formatter.
        foreach (FlagsListFormatter::format($info->flags) as $value) {
            $this->saucer->saucer_webview_options_append_browser_flag($options, $value);
        }

        // Define the "user-agent" header if it is specified.
        if ($info->userAgent !== null) {
            $this->saucer->saucer_webview_options_set_user_agent($options, $info->userAgent);
        }

        // Apply HW acceleration option
        $this->saucer->saucer_webview_options_set_hardware_acceleration(
            $options,
            $this->isHardwareAccelerationEnabled($window, $info),
        );
    }

    private function applyWebViewAfterCreated(CData $handle, Window $window, WebViewCreateInfo $info): void
    {
        $this->saucer->saucer_webview_set_dev_tools($handle, $this->isDevToolsEnabled($window, $info));
        $this->saucer->saucer_webview_set_context_menu($handle, $this->isContextMenuEnabled($window, $info));
        $this->saucer->saucer_webview_set_force_dark($handle, $this->isDarkModeEnabled($window, $info));
    }
}