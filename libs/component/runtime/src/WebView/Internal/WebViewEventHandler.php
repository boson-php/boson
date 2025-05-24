<?php

declare(strict_types=1);

namespace Boson\WebView\Internal;

use Boson\ApplicationPollerInterface;
use Boson\Component\Http\Request;
use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Internal\Saucer\SaucerPolicy;
use Boson\Internal\Saucer\SaucerState;
use Boson\Internal\Saucer\SaucerWebEvent as Event;
use Boson\Internal\WebView\CSaucerWebViewEventsStruct;
use Boson\WebView\Event\WebViewDomReady;
use Boson\WebView\Event\WebViewFaviconChanged;
use Boson\WebView\Event\WebViewFaviconChanging;
use Boson\WebView\Event\WebViewMessageReceived;
use Boson\WebView\Event\WebViewNavigated;
use Boson\WebView\Event\WebViewNavigating;
use Boson\WebView\Event\WebViewTitleChanged;
use Boson\WebView\Event\WebViewTitleChanging;
use Boson\WebView\WebView;
use Boson\WebView\WebViewState;
use FFI\CData;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView
 */
final class WebViewEventHandler
{
    /**
     * @var non-empty-string
     */
    private const string HANDLER_STRUCT = <<<'CDATA'
        struct {
            void (*onDomReady)(const saucer_handle *);
            void (*onNavigated)(const saucer_handle *, const char *);
            SAUCER_POLICY (*onNavigating)(const saucer_handle *, const saucer_navigation *);
            void (*onFaviconChanged)(const saucer_handle *, const saucer_icon *);
            void (*onTitleChanged)(const saucer_handle *, const char *);
            void (*onLoad)(const saucer_handle *, const SAUCER_STATE *);
        }
        CDATA;

    /**
     * Contains managed struct with event handlers.
     *
     * @phpstan-var CSaucerWebViewEventsStruct
     */
    private readonly CData $handlers;

    /**
     * Contains application-aware poller instance.
     */
    private readonly ApplicationPollerInterface $poller;

    public function __construct(
        private readonly LibSaucer $api,
        private readonly WebView $webview,
        private readonly EventDispatcherInterface $dispatcher,
        /**
         * @phpstan-ignore property.onlyWritten
         */
        private WebViewState &$state,
    ) {
        $this->poller = $this->webview->window->app->poller;

        $this->handlers = $this->createEventHandlers();

        $this->listenEvents();
    }

    private function changeState(WebViewState $state): void
    {
        $this->state = $state;
    }

    private function createEventHandlers(): CData
    {
        $struct = $this->api->new(self::HANDLER_STRUCT);

        $struct->onDomReady = $this->onSafeDomReady(...);
        $struct->onNavigated = $this->onSafeNavigated(...);
        $struct->onNavigating = $this->onSafeNavigating(...);
        $struct->onFaviconChanged = $this->onSafeFaviconChanged(...);
        $struct->onTitleChanged = $this->onSafeTitleChanged(...);
        $struct->onLoad = $this->onSafeLoad(...);

        return $struct;
    }

    public function listenEvents(): void
    {
        /** @phpstan-var CSaucerWebViewEventsStruct $ctx */
        $ctx = $this->handlers;

        $ptr = $this->webview->window->id->ptr;

        $this->api->saucer_webview_on($ptr, Event::SAUCER_WEB_EVENT_DOM_READY, $ctx->onDomReady);
        $this->api->saucer_webview_on($ptr, Event::SAUCER_WEB_EVENT_NAVIGATED, $ctx->onNavigated);
        $this->api->saucer_webview_on($ptr, Event::SAUCER_WEB_EVENT_NAVIGATE, $ctx->onNavigating);
        $this->api->saucer_webview_on($ptr, Event::SAUCER_WEB_EVENT_FAVICON, $ctx->onFaviconChanged);
        $this->api->saucer_webview_on($ptr, Event::SAUCER_WEB_EVENT_TITLE, $ctx->onTitleChanged);
        $this->api->saucer_webview_on($ptr, Event::SAUCER_WEB_EVENT_LOAD, $ctx->onLoad);

        $this->api->saucer_webview_on_message($ptr, $this->onSafeMessageReceived(...));
    }

    private function onMessageReceived(string $message): bool
    {
        $intention = $this->dispatcher->dispatch(new WebViewMessageReceived(
            subject: $this->webview,
            message: $message,
        ));

        return $intention->isPropagationStopped;
    }

    private function onSafeMessageReceived(string $message): bool
    {
        try {
            return $this->onMessageReceived($message);
        } catch (\Throwable $e) {
            $this->poller->fail($e);
        }

        return true;
    }

    private function onDomReady(CData $_): void
    {
        $this->changeState(WebViewState::Ready);

        $this->dispatcher->dispatch(new WebViewDomReady(
            subject: $this->webview,
        ));
    }

    private function onSafeDomReady(CData $_): void
    {
        try {
            $this->onDomReady($_);
        } catch (\Throwable $e) {
            $this->poller->fail($e);
        }
    }

    private function onNavigated(CData $_, string $url): void
    {
        try {
            $this->dispatcher->dispatch(new WebViewNavigated(
                subject: $this->webview,
                url: Request::castUrl($url),
            ));
        } catch (\Throwable $e) {
            $this->poller->fail($e);
        }
    }

    private function onSafeNavigated(CData $_, string $url): void
    {
        try {
            $this->onNavigated($_, $url);
        } catch (\Throwable $e) {
            $this->poller->fail($e);
        }
    }

    private function onNavigating(CData $_, CData $navigation): int
    {
        $this->changeState(WebViewState::Navigating);

        $url = \FFI::string($this->api->saucer_navigation_url($navigation));

        try {
            $intention = $this->dispatcher->dispatch(new WebViewNavigating(
                subject: $this->webview,
                url: Request::castUrl($url),
                isNewWindow: $this->api->saucer_navigation_new_window($navigation),
                isRedirection: $this->api->saucer_navigation_redirection($navigation),
                isUserInitiated: $this->api->saucer_navigation_user_initiated($navigation),
            ));

            return $intention->isCancelled
                ? SaucerPolicy::SAUCER_POLICY_BLOCK
                : SaucerPolicy::SAUCER_POLICY_ALLOW;
        } finally {
            $this->api->saucer_navigation_free($navigation);
        }
    }

    private function onSafeNavigating(CData $_, CData $navigation): int
    {
        try {
            return $this->onNavigating($_, $navigation);
        } catch (\Throwable $e) {
            $this->poller->fail($e);

            return SaucerPolicy::SAUCER_POLICY_BLOCK;
        }
    }

    private function onFaviconChanged(CData $ptr, CData $icon): void
    {
        $intention = $this->dispatcher->dispatch(new WebViewFaviconChanging($this->webview));

        try {
            if ($intention->isCancelled) {
                return;
            }

            $this->api->saucer_window_set_icon($ptr, $icon);
            $this->dispatcher->dispatch(new WebViewFaviconChanged($this->webview));
        } finally {
            $this->api->saucer_icon_free($icon);
        }
    }

    private function onSafeFaviconChanged(CData $ptr, CData $icon): void
    {
        try {
            $this->onFaviconChanged($ptr, $icon);
        } catch (\Throwable $e) {
            $this->poller->fail($e);
        }
    }

    private function onTitleChanged(CData $ptr, string $title): void
    {
        $intention = $this->dispatcher->dispatch(new WebViewTitleChanging(
            subject: $this->webview,
            title: $title,
        ));

        if ($intention->isCancelled) {
            return;
        }

        $this->api->saucer_window_set_title($ptr, $title);
        $this->dispatcher->dispatch(new WebViewTitleChanged($this->webview, $title));
    }

    private function onSafeTitleChanged(CData $ptr, string $title): void
    {
        try {
            $this->onTitleChanged($ptr, $title);
        } catch (\Throwable $e) {
            $this->poller->fail($e);
        }
    }

    private function onLoad(CData $_, CData $state): void
    {
        if ($state[0] === SaucerState::SAUCER_STATE_STARTED) {
            $this->changeState(WebViewState::Loading);

            return;
        }

        $this->changeState(WebViewState::Ready);
    }

    private function onSafeLoad(CData $_, CData $state): void
    {
        $this->onLoad($_, $state);
    }
}
