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
use Internal\Destroy\Destroyable as DestroyableInterface;

final class LifecycleEventsListener extends LoadedWindowExtension implements
    DestroyableInterface
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

    /**
     * @var array<WindowEvent::SAUCER_WINDOW_EVENT_*, int<0, max>>
     */
    private array $listeners;

    public function __construct(
        Window $window,
        EventListener $listener,
    ) {
        parent::__construct($window, $listener);

        $this->handlers = $this->saucer->new(self::WINDOW_HANDLER_STRUCT);

        $this->listeners = [
            WindowEvent::SAUCER_WINDOW_EVENT_DECORATED => $this->listenSaucerDecoratedEvent(),
            WindowEvent::SAUCER_WINDOW_EVENT_MAXIMIZE => $this->listenSaucerMaximizeEvent(),
            WindowEvent::SAUCER_WINDOW_EVENT_MINIMIZE => $this->listenSaucerMinimizeEvent(),
            WindowEvent::SAUCER_WINDOW_EVENT_CLOSED => $this->listenSaucerClosedEvent(),
            WindowEvent::SAUCER_WINDOW_EVENT_RESIZE => $this->listenSaucerResizeEvent(),
            WindowEvent::SAUCER_WINDOW_EVENT_FOCUS => $this->listenSaucerFocusEvent(),
            WindowEvent::SAUCER_WINDOW_EVENT_CLOSE => $this->listenSaucerClosingIntention(),
        ];
    }

    /**
     * @return int<0, max>
     */
    private function listenSaucerDecoratedEvent(): int
    {
        /** @var CSaucerWindowEventsStruct $handlers */
        $handlers = $this->handlers;
        $saucer = $this->saucer;
        $ptr = $this->ptr;

        $handlers->onDecorated = WeakClosure::create(function (CData $_, bool $decorated): void {
            $this->dispatch(new WindowDecorated(
                subject: $this->window,
                isDecorated: $decorated,
            ));
        });

        return $saucer->saucer_window_on(
            $ptr,
            WindowEvent::SAUCER_WINDOW_EVENT_DECORATED,
            $handlers->onDecorated,
            false,
            null,
        );
    }

    /**
     * @return int<0, max>
     */
    private function listenSaucerMaximizeEvent(): int
    {
        /** @var CSaucerWindowEventsStruct $handlers */
        $handlers = $this->handlers;
        $saucer = $this->saucer;
        $ptr = $this->ptr;

        $handlers->onMaximize = WeakClosure::create(function (CData $_, bool $maximized): void {
            $this->dispatch(new WindowMaximized(
                subject: $this->window,
                isMaximized: $maximized,
            ));
        });

        return $saucer->saucer_window_on(
            $ptr,
            WindowEvent::SAUCER_WINDOW_EVENT_MAXIMIZE,
            $handlers->onMaximize,
            false,
            null,
        );
    }

    /**
     * @return int<0, max>
     */
    private function listenSaucerMinimizeEvent(): int
    {
        /** @var CSaucerWindowEventsStruct $handlers */
        $handlers = $this->handlers;
        $saucer = $this->saucer;
        $ptr = $this->ptr;

        $handlers->onMinimize = WeakClosure::create(function (CData $_, bool $minimized): void {
            $this->dispatch(new WindowMinimized(
                subject: $this->window,
                isMinimized: $minimized,
            ));
        });

        return $saucer->saucer_window_on(
            $ptr,
            WindowEvent::SAUCER_WINDOW_EVENT_MINIMIZE,
            $handlers->onMinimize,
            false,
            null,
        );
    }

    /**
     * @return int<0, max>
     */
    private function listenSaucerClosingIntention(): int
    {
        /** @var CSaucerWindowEventsStruct $handlers */
        $handlers = $this->handlers;
        $saucer = $this->saucer;
        $ptr = $this->ptr;

        $handlers->onClosing = WeakClosure::create(function (): int {
            $this->dispatch($intention = new WindowClosing($this->window));

            if ($intention->isCancelled) {
                return Policy::SAUCER_POLICY_BLOCK;
            }

            return Policy::SAUCER_POLICY_ALLOW;
        });

        return $saucer->saucer_window_on(
            $ptr,
            WindowEvent::SAUCER_WINDOW_EVENT_CLOSE,
            $handlers->onClosing,
            false,
            null,
        );
    }

    /**
     * @return int<0, max>
     */
    private function listenSaucerClosedEvent(): int
    {
        /** @var CSaucerWindowEventsStruct $handlers */
        $handlers = $this->handlers;
        $saucer = $this->saucer;
        $ptr = $this->ptr;

        $handlers->onClosed = WeakClosure::create(function (): void {
            $this->dispatch(new WindowClosed($this->window));
        });

        return $saucer->saucer_window_on(
            $ptr,
            WindowEvent::SAUCER_WINDOW_EVENT_CLOSED,
            $handlers->onClosed,
            false,
            null,
        );
    }

    /**
     * @return int<0, max>
     */
    private function listenSaucerResizeEvent(): int
    {
        /** @var CSaucerWindowEventsStruct $handlers */
        $handlers = $this->handlers;
        $saucer = $this->saucer;
        $ptr = $this->ptr;

        $handlers->onResize = WeakClosure::create(function (CData $_, int $width, int $height): void {
            $this->dispatch(new WindowResized(
                subject: $this->window,
                width: $width,
                height: $height,
            ));
        });

        return $saucer->saucer_window_on(
            $ptr,
            WindowEvent::SAUCER_WINDOW_EVENT_RESIZE,
            $handlers->onResize,
            false,
            null,
        );
    }

    /**
     * @return int<0, max>
     */
    private function listenSaucerFocusEvent(): int
    {
        /** @var CSaucerWindowEventsStruct $handlers */
        $handlers = $this->handlers;
        $saucer = $this->saucer;
        $ptr = $this->ptr;

        $handlers->onFocus = WeakClosure::create(function (CData $_, bool $focused): void {
            $this->dispatch(new WindowFocused(
                subject: $this->window,
                isFocused: $focused,
            ));
        });

        return $saucer->saucer_window_on(
            $ptr,
            WindowEvent::SAUCER_WINDOW_EVENT_FOCUS,
            $handlers->onFocus,
            false,
            null,
        );
    }

    /**
     * @internal for internal usage only
     */
    public function destroy(): void
    {
        foreach ($this->listeners as $event => $id) {
            $this->saucer->saucer_window_off($this->ptr, $event, $id);
        }

        $this->handlers->onDecorated = null;
        $this->handlers->onMaximize = null;
        $this->handlers->onMinimize = null;
        $this->handlers->onClosing = null;
        $this->handlers->onClosed = null;
        $this->handlers->onResize = null;
        $this->handlers->onFocus = null;

        \FFI::free(\FFI::addr($this->handlers));

        $this->listeners = [];
    }
}
