<?php

declare(strict_types=1);

namespace Boson\WebView\Api\LifecycleEvents;

use Boson\Component\Http\Request;
use Boson\Component\Saucer\Policy;
use Boson\Component\Saucer\State;
use Boson\Component\Saucer\Status;
use Boson\Component\Saucer\WebViewEvent;
use Boson\Component\WeakType\WeakClosure;
use Boson\Dispatcher\EventListener;
use Boson\Internal\WebView\CSaucerWebViewEventsStruct;
use Boson\WebView\Api\LoadedWebViewExtension;
use Boson\WebView\Event\WebViewDomReady;
use Boson\WebView\Event\WebViewFaviconChanged;
use Boson\WebView\Event\WebViewFaviconChanging;
use Boson\WebView\Event\WebViewMessageReceived;
use Boson\WebView\Event\WebViewNavigated;
use Boson\WebView\Event\WebViewNavigating;
use Boson\WebView\Event\WebViewStateChanged;
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
            // saucer_webview_event_permission
            SAUCER_STATUS (*onPermissionRequested)(const saucer_webview *, saucer_permission_request *, void *);

            // saucer_webview_event_fullscreen
            void (*onFullscreen)(const saucer_webview *, bool, void *);

            // saucer_webview_event_dom_ready
            void (*onDomReady)(const saucer_webview *, void *);

            // saucer_webview_event_navigated
            void (*onNavigated)(const saucer_webview *, const saucer_url *, void *);

            // saucer_webview_event_navigate
            SAUCER_POLICY (*onNavigating)(const saucer_webview *, const saucer_navigation *, void *);

            // saucer_webview_event_message
            SAUCER_STATUS (*onMessage)(const saucer_webview *, const char *, size_t, void *);

            // saucer_webview_event_request
            void (*onRequest)(const saucer_webview *, const saucer_url *, void *);

            // saucer_webview_event_favicon
            void (*onFaviconChanged)(const saucer_webview *, saucer_icon *, void *);

            // saucer_webview_event_title
            void (*onTitleChanged)(const saucer_webview *, const char *, size_t, void *);

            // saucer_webview_event_load
            void (*onLoad)(const saucer_webview *, SAUCER_STATE, void *);
        }
        CDATA;

    /**
     * Contains a managed struct with event handlers.
     *
     * @phpstan-var CSaucerWebViewEventsStruct
     */
    private readonly CData $handlers;

    /**
     * @var array<WebViewEvent::SAUCER_WEBVIEW_EVENT_*, int<0, max>>
     */
    private array $listeners;

    private readonly \ReflectionProperty $state;

    public function __construct(
        WebView $webview,
        EventListener $listener,
    ) {
        parent::__construct($webview, $listener);

        $this->state = new \ReflectionProperty($this->webview, 'state');

        $this->handlers = $this->app->saucer->new(self::WEBVIEW_HANDLER_STRUCT);

        $this->listeners = [
            // WebViewEvent::SAUCER_WEBVIEW_EVENT_PERMISSION => ...,
            // WebViewEvent::SAUCER_WEBVIEW_EVENT_FULLSCREEN => ...,
            WebViewEvent::SAUCER_WEBVIEW_EVENT_DOM_READY => $this->listenSaucerDomReadyEvent(...),
            WebViewEvent::SAUCER_WEBVIEW_EVENT_NAVIGATED => $this->listenSaucerNavigatedEvent(...),
            WebViewEvent::SAUCER_WEBVIEW_EVENT_NAVIGATE => $this->listenSaucerNavigatingEvent(...),
            WebViewEvent::SAUCER_WEBVIEW_EVENT_MESSAGE => $this->listenSaucerMessageEvent(...),
            // WebViewEvent::SAUCER_WEBVIEW_EVENT_REQUEST => ...,
            WebViewEvent::SAUCER_WEBVIEW_EVENT_FAVICON => $this->listenSaucerFaviconChangedEvent(...),
            WebViewEvent::SAUCER_WEBVIEW_EVENT_TITLE => $this->listenSaucerTitleChangedEvent(...),
            WebViewEvent::SAUCER_WEBVIEW_EVENT_LOAD => $this->listenSaucerLoadEvent(...),
        ];
    }

    /**
     * @return int<0, max>
     */
    private function listenSaucerDomReadyEvent(): int
    {
        /** @phpstan-var CSaucerWebViewEventsStruct $handlers */
        $handlers = $this->handlers;
        $saucer = $this->saucer;
        $ptr = $this->ptr;

        $handlers->onDomReady = WeakClosure::create(function (): void {
            try {
                $this->changeState(WebViewState::Ready);

                $this->dispatch(new WebViewDomReady(
                    subject: $this->webview,
                ));
            } catch (\Throwable $e) {
                $this->webview->window->app->poller->throw($e);
            }
        });

        return $saucer->saucer_webview_on(
            $ptr,
            WebViewEvent::SAUCER_WEBVIEW_EVENT_DOM_READY,
            $handlers->onDomReady,
            false,
            null,
        );
    }

    /**
     * @return int<0, max>
     */
    private function listenSaucerNavigatedEvent(): int
    {
        /** @phpstan-var CSaucerWebViewEventsStruct $handlers */
        $handlers = $this->handlers;
        $saucer = $this->saucer;
        $ptr = $this->ptr;

        $handlers->onNavigated = WeakClosure::create(function (CData $_, CData $url): void {
            try {
                $this->dispatch(new WebViewNavigated(
                    subject: $this->webview,
                    url: Request::castUrl($this->urlToString($url)),
                ));
            } catch (\Throwable $e) {
                $this->webview->window->app->poller->throw($e);
            }
        });

        return $saucer->saucer_webview_on(
            $ptr,
            WebViewEvent::SAUCER_WEBVIEW_EVENT_NAVIGATED,
            $handlers->onNavigated,
            false,
            null,
        );
    }

    /**
     * @return int<0, max>
     */
    private function listenSaucerNavigatingEvent(): int
    {
        /** @phpstan-var CSaucerWebViewEventsStruct $handlers */
        $handlers = $this->handlers;
        $saucer = $this->saucer;
        $ptr = $this->ptr;

        $handlers->onNavigating = WeakClosure::create(function (CData $_, CData $navigation): int {
            try {
                $this->changeState(WebViewState::Navigating);

                $saucerUrl = $this->app->saucer->saucer_navigation_url($navigation);
                $bosonUrl = Request::castUrl($this->urlToString($saucerUrl));
                $this->app->saucer->saucer_url_free($saucerUrl);

                $isNavigationAllowed = $this->intent(new WebViewNavigating(
                    subject: $this->webview,
                    url: $bosonUrl,
                    isNewWindow: $this->app->saucer->saucer_navigation_new_window($navigation),
                    isRedirection: $this->app->saucer->saucer_navigation_redirection($navigation),
                    isUserInitiated: $this->app->saucer->saucer_navigation_user_initiated($navigation),
                ));

                if ($isNavigationAllowed) {
                    return Policy::SAUCER_POLICY_ALLOW;
                }
            } catch (\Throwable $e) {
                $this->webview->window->app->poller->throw($e);
            }

            return Policy::SAUCER_POLICY_BLOCK;
        });

        return $saucer->saucer_webview_on(
            $ptr,
            WebViewEvent::SAUCER_WEBVIEW_EVENT_NAVIGATE,
            $handlers->onNavigating,
            false,
            null,
        );
    }

    /**
     * @return int<0, max>
     */
    private function listenSaucerMessageEvent(): int
    {
        /** @phpstan-var CSaucerWebViewEventsStruct $handlers */
        $handlers = $this->handlers;
        $saucer = $this->saucer;
        $ptr = $this->ptr;

        $handlers->onMessage = WeakClosure::create(function (CData $_, string $message, int $size): int {
            try {
                $this->dispatch($event = new WebViewMessageReceived(
                    subject: $this->webview,
                    message: $message,
                ));

                if ($event->isPropagationStopped) {
                    return Status::SAUCER_STATE_HANDLED;
                }
            } catch (\Throwable $e) {
                $this->webview->window->app->poller->throw($e);
            }

            return Status::SAUCER_STATE_UNHANDLED;
        });

        return $saucer->saucer_webview_on(
            $ptr,
            WebViewEvent::SAUCER_WEBVIEW_EVENT_MESSAGE,
            $handlers->onMessage,
            false,
            null,
        );
    }

    /**
     * @return int<0, max>
     */
    private function listenSaucerFaviconChangedEvent(): int
    {
        /** @phpstan-var CSaucerWebViewEventsStruct $handlers */
        $handlers = $this->handlers;
        $saucer = $this->saucer;
        $ptr = $this->ptr;

        $handlers->onFaviconChanged = WeakClosure::create(function (CData $_, CData $icon): void {
            try {
                if (!$this->intent(new WebViewFaviconChanging($this->webview))) {
                    return;
                }

                $this->app->saucer->saucer_window_set_icon($this->webview->window->id->ptr, $icon);

                $this->dispatch(new WebViewFaviconChanged($this->webview));
            } catch (\Throwable $e) {
                $this->webview->window->app->poller->throw($e);
            }
        });

        return $saucer->saucer_webview_on(
            $ptr,
            WebViewEvent::SAUCER_WEBVIEW_EVENT_FAVICON,
            $handlers->onFaviconChanged,
            false,
            null,
        );
    }

    /**
     * @return int<0, max>
     */
    private function listenSaucerTitleChangedEvent(): int
    {
        /** @phpstan-var CSaucerWebViewEventsStruct $handlers */
        $handlers = $this->handlers;
        $saucer = $this->saucer;
        $ptr = $this->ptr;

        $handlers->onTitleChanged = WeakClosure::create(function (CData $_, string $title, int $length): void {
            try {
                if (!$this->intent(new WebViewTitleChanging($this->webview, $title))) {
                    return;
                }

                $this->app->saucer->saucer_window_set_title($this->window->id->ptr, $title);
                $this->dispatch(new WebViewTitleChanged($this->webview, $title));
            } catch (\Throwable $e) {
                $this->webview->window->app->poller->throw($e);
            }
        });

        return $saucer->saucer_webview_on(
            $ptr,
            WebViewEvent::SAUCER_WEBVIEW_EVENT_TITLE,
            $handlers->onTitleChanged,
            false,
            null,
        );
    }

    /**
     * @return int<0, max>
     */
    private function listenSaucerLoadEvent(): int
    {
        /** @phpstan-var CSaucerWebViewEventsStruct $handlers */
        $handlers = $this->handlers;
        $saucer = $this->saucer;
        $ptr = $this->ptr;

        $handlers->onLoad = WeakClosure::create(function (CData $_, int $state): void {
            try {
                if ($state === State::SAUCER_STATE_STARTED) {
                    $this->changeState(WebViewState::Loading);

                    return;
                }

                $this->changeState(WebViewState::Ready);
            } catch (\Throwable $e) {
                $this->webview->window->app->poller->throw($e);
            }
        });

        return $saucer->saucer_webview_on(
            $ptr,
            WebViewEvent::SAUCER_WEBVIEW_EVENT_LOAD,
            $handlers->onLoad,
            false,
            null,
        );
    }

    private function changeState(WebViewState $state): void
    {
        $before = $this->state->getRawValue($this->webview);

        if ($before === $state) {
            return;
        }

        $this->state->setRawValue($this->webview, $state);

        $this->dispatch(new WebViewStateChanged(
            subject: $this->webview,
            state: $state,
        ));
    }

    private function urlToString(CData $url): string
    {
        $length = $this->app->saucer->new('size_t');
        $this->app->saucer->saucer_url_string($url, null, \FFI::addr($length));

        if ($length->cdata === 0) {
            return '';
        }

        $value = $this->app->saucer->new("char[{$length->cdata}]");
        $this->app->saucer->saucer_url_string($url, \FFI::addr($value[0]), \FFI::addr($length));

        return \FFI::string(\FFI::addr($value[0]), $length->cdata);
    }
}
