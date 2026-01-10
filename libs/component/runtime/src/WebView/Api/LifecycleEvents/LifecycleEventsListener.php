<?php

declare(strict_types=1);

namespace Boson\WebView\Api\LifecycleEvents;

use Boson\Component\Http\Request;
use Boson\Component\Saucer\Policy;
use Boson\Component\Saucer\State;
use Boson\Component\Saucer\WebViewEvent as Event;
use Boson\Dispatcher\EventListener;
use Boson\Internal\WebView\CSaucerWebViewEventsStruct;
use Boson\WebView\Api\LoadedWebViewExtension;
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
final class LifecycleEventsListener extends LoadedWebViewExtension
{
    /**
     * @var non-empty-string
     */
    private const string WEBVIEW_HANDLER_STRUCT = <<<'CDATA'
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

    private readonly \ReflectionProperty $state;

    public function __construct(
        WebView $webview,
        EventListener $listener,
    ) {
        parent::__construct($webview, $listener);

        $this->state = new \ReflectionProperty($this->webview, 'state');

        $this->handlers = $this->createEventHandlers();

        $this->listenEvents();
    }

    private function changeState(WebViewState $state): void
    {
        $this->state->setRawValue($this->webview, $state);
    }

    private function createEventHandlers(): CData
    {
        $struct = $this->app->saucer->new(self::WEBVIEW_HANDLER_STRUCT);

        $struct->onDomReady = $this->onSafeDomReady(...);
        $struct->onNavigated = $this->onSafeNavigated(...);
        $struct->onNavigating = $this->onSafeNavigating(...);
        $struct->onFaviconChanged = $this->onSafeFaviconChanged(...);
        $struct->onTitleChanged = $this->onSafeTitleChanged(...);
        $struct->onLoad = $this->onSafeLoad(...);

        return $struct;
    }

    private function listenEvents(): void
    {
        /** @phpstan-var CSaucerWebViewEventsStruct $ctx */
        $ctx = $this->handlers;

        $ptr = $this->webview->window->id->ptr;

        $this->app->saucer->saucer_webview_on($ptr, Event::SAUCER_WEBVIEW_EVENT_DOM_READY, $ctx->onDomReady);
        $this->app->saucer->saucer_webview_on($ptr, Event::SAUCER_WEBVIEW_EVENT_NAVIGATED, $ctx->onNavigated);
        $this->app->saucer->saucer_webview_on($ptr, Event::SAUCER_WEBVIEW_EVENT_NAVIGATE, $ctx->onNavigating);
        $this->app->saucer->saucer_webview_on($ptr, Event::SAUCER_WEBVIEW_EVENT_FAVICON, $ctx->onFaviconChanged);
        $this->app->saucer->saucer_webview_on($ptr, Event::SAUCER_WEBVIEW_EVENT_TITLE, $ctx->onTitleChanged);
        $this->app->saucer->saucer_webview_on($ptr, Event::SAUCER_WEBVIEW_EVENT_LOAD, $ctx->onLoad);

        $this->app->saucer->saucer_webview_on_message($ptr, $this->onSafeMessageReceived(...));
    }

    private function onMessageReceived(string $message): bool
    {
        $this->dispatch($event = new WebViewMessageReceived(
            subject: $this->webview,
            message: $message,
        ));

        return $event->isPropagationStopped;
    }

    private function onSafeMessageReceived(string $message): bool
    {
        try {
            return $this->onMessageReceived($message);
        } catch (\Throwable $e) {
            $this->webview->window->app->poller->throw($e);
        }

        return true;
    }

    private function onDomReady(CData $_): void
    {
        $this->changeState(WebViewState::Ready);

        $this->dispatch(new WebViewDomReady(
            subject: $this->webview,
        ));
    }

    private function onSafeDomReady(CData $_): void
    {
        try {
            $this->onDomReady($_);
        } catch (\Throwable $e) {
            $this->webview->window->app->poller->throw($e);
        }
    }

    private function onNavigated(CData $_, string $url): void
    {
        try {
            $this->dispatch(new WebViewNavigated(
                subject: $this->webview,
                url: Request::castUrl($url),
            ));
        } catch (\Throwable $e) {
            $this->webview->window->app->poller->throw($e);
        }
    }

    private function onSafeNavigated(CData $_, string $url): void
    {
        try {
            $this->onNavigated($_, $url);
        } catch (\Throwable $e) {
            $this->webview->window->app->poller->throw($e);
        }
    }

    private function onNavigating(CData $_, CData $navigation): int
    {
        $this->changeState(WebViewState::Navigating);

        $url = \FFI::string($this->app->saucer->saucer_navigation_url($navigation));

        try {
            return $this->intent(new WebViewNavigating(
                subject: $this->webview,
                url: Request::castUrl($url),
                isNewWindow: $this->app->saucer->saucer_navigation_new_window($navigation),
                isRedirection: $this->app->saucer->saucer_navigation_redirection($navigation),
                isUserInitiated: $this->app->saucer->saucer_navigation_user_initiated($navigation),
            ))
                ? Policy::SAUCER_POLICY_ALLOW
                : Policy::SAUCER_POLICY_BLOCK;
        } finally {
            $this->app->saucer->saucer_navigation_free($navigation);
        }
    }

    private function onSafeNavigating(CData $_, CData $navigation): int
    {
        try {
            return $this->onNavigating($_, $navigation);
        } catch (\Throwable $e) {
            $this->webview->window->app->poller->throw($e);

            return Policy::SAUCER_POLICY_BLOCK;
        }
    }

    private function onFaviconChanged(CData $ptr, CData $icon): void
    {
        if (!$this->intent(new WebViewFaviconChanging($this->webview))) {
            return;
        }

        try {
            $this->app->saucer->saucer_window_set_icon($ptr, $icon);

            $this->dispatch(new WebViewFaviconChanged($this->webview));
        } finally {
            $this->app->saucer->saucer_icon_free($icon);
        }
    }

    private function onSafeFaviconChanged(CData $ptr, CData $icon): void
    {
        try {
            $this->onFaviconChanged($ptr, $icon);
        } catch (\Throwable $e) {
            $this->webview->window->app->poller->throw($e);
        }
    }

    private function onTitleChanged(CData $ptr, string $title): void
    {
        if (!$this->intent(new WebViewTitleChanging($this->webview, $title))) {
            return;
        }

        $this->app->saucer->saucer_window_set_title($ptr, $title);
        $this->dispatch(new WebViewTitleChanged($this->webview, $title));
    }

    private function onSafeTitleChanged(CData $ptr, string $title): void
    {
        try {
            $this->onTitleChanged($ptr, $title);
        } catch (\Throwable $e) {
            $this->webview->window->app->poller->throw($e);
        }
    }

    private function onLoad(CData $_, CData $state): void
    {
        if ($state[0] === State::SAUCER_STATE_STARTED) {
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
