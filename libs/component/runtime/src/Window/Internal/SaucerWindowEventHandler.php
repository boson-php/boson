<?php

declare(strict_types=1);

namespace Boson\Window\Internal;

use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Internal\Saucer\SaucerPolicy;
use Boson\Internal\Saucer\SaucerWindowEvent;
use Boson\Internal\Window\CSaucerWindowEventsStruct;
use Boson\Window\Event\WindowClosed;
use Boson\Window\Event\WindowClosing;
use Boson\Window\Event\WindowDecorated;
use Boson\Window\Event\WindowFocused;
use Boson\Window\Event\WindowMaximized;
use Boson\Window\Event\WindowMinimized;
use Boson\Window\Event\WindowResized;
use Boson\Window\Window;
use FFI\CData;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Window
 */
final readonly class SaucerWindowEventHandler
{
    /**
     * @var non-empty-string
     */
    private const string WINDOW_HANDLER_STRUCT = <<<'CDATA'
        struct {
            void (*onDecorated)(const saucer_handle *, bool decorated);
            void (*onMaximize)(const saucer_handle *, bool state);
            void (*onMinimize)(const saucer_handle *, bool state);
            SAUCER_POLICY (*onClosing)(const saucer_handle *);
            void (*onClosed)(const saucer_handle *);
            void (*onResize)(const saucer_handle *, int width, int height);
            void (*onFocus)(const saucer_handle *, bool focus);
        }
        CDATA;

    /**
     * Contains managed struct with event handlers.
     *
     * @phpstan-var CSaucerWindowEventsStruct
     */
    private CData $handlers;

    public function __construct(
        private LibSaucer $api,
        private Window $window,
        private EventDispatcherInterface $dispatcher,
    ) {
        $this->handlers = $this->createEventHandlers();

        $this->listenEvents();
    }

    private function createEventHandlers(): CData
    {
        $struct = $this->api->new(self::WINDOW_HANDLER_STRUCT);

        $struct->onDecorated = $this->onDecorated(...);
        $struct->onMaximize = $this->onMaximize(...);
        $struct->onMinimize = $this->onMinimize(...);
        $struct->onClosing = $this->onClosing(...);
        $struct->onClosed = $this->onClosed(...);
        $struct->onResize = $this->onResize(...);
        $struct->onFocus = $this->onFocus(...);

        return $struct;
    }

    public function listenEvents(): void
    {
        /** @var CSaucerWindowEventsStruct $handlers */
        $handlers = $this->handlers;

        $ptr = $this->window->id->ptr;

        $this->api->saucer_window_on($ptr, SaucerWindowEvent::SAUCER_WINDOW_EVENT_DECORATED, $handlers->onDecorated);
        $this->api->saucer_window_on($ptr, SaucerWindowEvent::SAUCER_WINDOW_EVENT_MAXIMIZE, $handlers->onMaximize);
        $this->api->saucer_window_on($ptr, SaucerWindowEvent::SAUCER_WINDOW_EVENT_MINIMIZE, $handlers->onMinimize);
        $this->api->saucer_window_on($ptr, SaucerWindowEvent::SAUCER_WINDOW_EVENT_CLOSE, $handlers->onClosing);
        $this->api->saucer_window_on($ptr, SaucerWindowEvent::SAUCER_WINDOW_EVENT_CLOSED, $handlers->onClosed);
        $this->api->saucer_window_on($ptr, SaucerWindowEvent::SAUCER_WINDOW_EVENT_RESIZE, $handlers->onResize);
        $this->api->saucer_window_on($ptr, SaucerWindowEvent::SAUCER_WINDOW_EVENT_FOCUS, $handlers->onFocus);
    }

    private function onDecorated(CData $_, bool $decorated): void
    {
        $this->dispatcher->dispatch(new WindowDecorated(
            subject: $this->window,
            isDecorated: $decorated,
        ));
    }

    private function onMaximize(CData $_, bool $state): void
    {
        $this->dispatcher->dispatch(new WindowMaximized(
            subject: $this->window,
            isMaximized: $state,
        ));
    }

    private function onMinimize(CData $_, bool $state): void
    {
        $this->dispatcher->dispatch(new WindowMinimized(
            subject: $this->window,
            isMinimized: $state,
        ));
    }

    /**
     * @return SaucerPolicy::SAUCER_POLICY_*
     */
    private function onClosing(CData $_): int
    {
        $event = $this->dispatcher->dispatch(new WindowClosing($this->window));

        return $event->isCancelled
            ? SaucerPolicy::SAUCER_POLICY_BLOCK
            : SaucerPolicy::SAUCER_POLICY_ALLOW;
    }

    private function onClosed(CData $_): void
    {
        $this->dispatcher->dispatch(new WindowClosed($this->window));
    }

    /**
     * @param int<0, 2147483647> $width
     * @param int<0, 2147483647> $height
     */
    private function onResize(CData $_, int $width, int $height): void
    {
        $this->dispatcher->dispatch(new WindowResized(
            subject: $this->window,
            width: $width,
            height: $height,
        ));
    }

    private function onFocus(CData $_, bool $focus): void
    {
        $this->dispatcher->dispatch(new WindowFocused(
            subject: $this->window,
            isFocused: $focus,
        ));
    }
}
