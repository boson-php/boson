<?php

declare(strict_types=1);

namespace Boson\Window\Api\LifecycleEvents;

use Boson\Component\Saucer\Policy;
use Boson\Component\Saucer\WindowEvent;
use Boson\Component\WeakType\WeakClosure;
use Boson\Dispatcher\EventListener;
use Boson\Internal\Window\CSaucerWindowEventsStruct;
use Boson\Window\Api\LoadedWindowExtension;
use Boson\Window\Event\WindowClosed;
use Boson\Window\Event\WindowClosing;
use Boson\Window\Event\WindowDecorated;
use Boson\Window\Event\WindowFocused;
use Boson\Window\Event\WindowMaximized;
use Boson\Window\Event\WindowMinimized;
use Boson\Window\Event\WindowResized;
use Boson\Window\Window;
use FFI\CData;

final class WindowLifecycleEventsListener extends LoadedWindowExtension
{
    /**
     * @var non-empty-string
     */
    private const string WINDOW_HANDLER_STRUCT = <<<'CDATA'
        struct {
            // saucer_window_event_decorated
            void (*onDecorated)(const saucer_window *, SAUCER_WINDOW_DECORATION, void *);

            // saucer_window_event_maximize
            void (*onMaximize)(const saucer_window *, bool state, void *);

            // saucer_window_event_minimize
            void (*onMinimize)(const saucer_window *, bool state, void *);

            // saucer_window_event_close
            SAUCER_POLICY (*onClosing)(const saucer_window *, void *);

            // saucer_window_event_closed
            void (*onClosed)(const saucer_window *, void *);

            // saucer_window_event_resize
            void (*onResize)(const saucer_window *, int width, int height, void *);

            // saucer_window_event_focus
            void (*onFocus)(const saucer_window *, bool focus, void *);
        }
        CDATA;

    /**
     * Contains managed struct with event handlers.
     *
     * @phpstan-var CSaucerWindowEventsStruct
     */
    private readonly CData $handlers;

    public function __construct(
        Window $window,
        EventListener $listener,
    ) {
        parent::__construct($window, $listener);

        $this->handlers = $this->createEventHandlers();

        $this->listenEvents();
    }

    private function createEventHandlers(): CData
    {
        $struct = $this->app->saucer->new(self::WINDOW_HANDLER_STRUCT);

        //$struct->onDecorated = $this->onDecorated(...);
        //$struct->onMaximize = $this->onMaximize(...);
        //$struct->onMinimize = $this->onMinimize(...);
        //$struct->onClosing = $this->onClosing(...);
        $struct->onClosed = WeakClosure::create(function (CData $_) {
            $this->onClosed($_);
        });
        //$struct->onResize = $this->onResize(...);
        //$struct->onFocus = $this->onFocus(...);

        return $struct;
    }

    public function listenEvents(): void
    {
        /** @var CSaucerWindowEventsStruct $handlers */
        $handlers = $this->handlers;

        $ptr = $this->id->ptr;

        //$this->api->saucer_window_on($ptr, WindowEvent::SAUCER_WINDOW_EVENT_DECORATED, $handlers->onDecorated, false, null);
        //$this->api->saucer_window_on($ptr, WindowEvent::SAUCER_WINDOW_EVENT_MAXIMIZE, $handlers->onMaximize, false, null);
        //$this->api->saucer_window_on($ptr, WindowEvent::SAUCER_WINDOW_EVENT_MINIMIZE, $handlers->onMinimize, false, null);
        //$this->api->saucer_window_on($ptr, WindowEvent::SAUCER_WINDOW_EVENT_CLOSE, $handlers->onClosing, false, null);
        $this->app->saucer->saucer_window_on($ptr, WindowEvent::SAUCER_WINDOW_EVENT_CLOSED, $handlers->onClosed, false, null);
        //$this->api->saucer_window_on($ptr, WindowEvent::SAUCER_WINDOW_EVENT_RESIZE, $handlers->onResize, false, null);
        //$this->api->saucer_window_on($ptr, WindowEvent::SAUCER_WINDOW_EVENT_FOCUS, $handlers->onFocus, false, null);
    }

    private function onDecorated(CData $_, bool $decorated): void
    {
        $this->dispatch(new WindowDecorated(
            subject: $this->window,
            isDecorated: $decorated,
        ));
    }

    private function onMaximize(CData $_, bool $state): void
    {
        $this->dispatch(new WindowMaximized(
            subject: $this->window,
            isMaximized: $state,
        ));
    }

    private function onMinimize(CData $_, bool $state): void
    {
        $this->dispatch(new WindowMinimized(
            subject: $this->window,
            isMinimized: $state,
        ));
    }

    /**
     * @return Policy::SAUCER_POLICY_*
     */
    private function onClosing(CData $_): int
    {
        $this->dispatch($intention = new WindowClosing($this->window));

        return $intention->isCancelled
            ? Policy::SAUCER_POLICY_BLOCK
            : Policy::SAUCER_POLICY_ALLOW;
    }

    private function onClosed(CData $_): void
    {
        $this->dispatch(new WindowClosed($this->window));
    }

    /**
     * @param int<0, 2147483647> $width
     * @param int<0, 2147483647> $height
     */
    private function onResize(CData $_, int $width, int $height): void
    {
        $this->dispatch(new WindowResized(
            subject: $this->window,
            width: $width,
            height: $height,
        ));
    }

    private function onFocus(CData $_, bool $focus): void
    {
        $this->dispatch(new WindowFocused(
            subject: $this->window,
            isFocused: $focus,
        ));
    }
}
