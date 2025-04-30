<?php

declare(strict_types=1);

namespace Boson\WebView\Internal;

use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Http\Request;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Internal\Saucer\SaucerPolicy;
use Boson\Internal\Saucer\SaucerState;
use Boson\Internal\Saucer\SaucerWebEvent as Event;
use Boson\Internal\WebView\CSaucerWebViewEventsStruct;
use Boson\WebView\Event\WebViewDomReady;
use Boson\WebView\Event\WebViewFaviconChanged;
use Boson\WebView\Event\WebViewFaviconChanging;
use Boson\WebView\Event\WebViewMessageReceiving;
use Boson\WebView\Event\WebViewNavigated;
use Boson\WebView\Event\WebViewNavigating;
use Boson\WebView\Event\WebViewTitleChanged;
use Boson\WebView\Event\WebViewTitleChanging;
use Boson\WebView\State;
use Boson\WebView\WebView;
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

    public function __construct(
        private readonly LibSaucer $api,
        private readonly WebView $webview,
        private readonly EventDispatcherInterface $dispatcher,
        /**
         * @phpstan-ignore property.onlyWritten
         */
        private State &$state,
    ) {
        $this->handlers = $this->createEventHandlers();

        $this->listenEvents();
    }

    private function changeState(State $state): void
    {
        $this->state = $state;
    }

    private function createEventHandlers(): CData
    {
        $struct = $this->api->new(self::HANDLER_STRUCT);

        $struct->onDomReady = $this->onDomReady(...);
        $struct->onNavigated = $this->onNavigated(...);
        $struct->onNavigating = $this->onNavigating(...);
        $struct->onFaviconChanged = $this->onFaviconChanged(...);
        $struct->onTitleChanged = $this->onTitleChanged(...);
        $struct->onLoad = $this->onLoad(...);

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

        $this->api->saucer_webview_on_message($ptr, $this->onMessageReceived(...));
    }

    private function onMessageReceived(string $message): bool
    {
        $intention = $this->dispatcher->dispatch(new WebViewMessageReceiving(
            subject: $this->webview,
            message: $message,
        ));

        return $intention->isCancelled;
    }

    private function onDomReady(CData $_): void
    {
        $this->dispatcher->dispatch(new WebViewDomReady(
            subject: $this->webview,
        ));
    }

    private function onNavigated(CData $_, string $url): void
    {
        $this->dispatcher->dispatch(new WebViewNavigated(
            subject: $this->webview,
            url: Request::castUrl($url),
        ));
    }

    private function onNavigating(CData $_, CData $navigation): int
    {
        $this->changeState(State::Navigating);

        $url = \FFI::string($this->api->saucer_navigation_url($navigation));

        $intention = $this->dispatcher->dispatch(new WebViewNavigating(
            subject: $this->webview,
            url: Request::castUrl($url),
            isNewWindow: $this->api->saucer_navigation_new_window($navigation),
            isRedirection: $this->api->saucer_navigation_redirection($navigation),
            isUserInitiated: $this->api->saucer_navigation_user_initiated($navigation),
        ));

        $this->api->saucer_navigation_free($navigation);

        return $intention->isCancelled
            ? SaucerPolicy::SAUCER_POLICY_BLOCK
            : SaucerPolicy::SAUCER_POLICY_ALLOW;
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

    private function onLoad(CData $_, CData $state): void
    {
        if ($state[0] === SaucerState::SAUCER_STATE_STARTED) {
            $this->changeState(State::Loading);

            return;
        }

        $this->changeState(State::Ready);
    }
}
