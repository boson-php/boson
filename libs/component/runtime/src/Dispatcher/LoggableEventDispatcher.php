<?php

declare(strict_types=1);

namespace Boson\Dispatcher;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class LoggableEventDispatcher implements EventDispatcherInterface
{
    private ?string $last = null;

    public function __construct(
        private readonly EventDispatcherInterface $delegate,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {}

    public function dispatch(object $event): object
    {
        if ($this->last !== $event::class) {
            $this->logger->debug('[Event] Dispatched {name}', [
                'name' => $event::class,
                'event' => $event,
            ]);

            $this->last = $event::class;
        }

        return $this->delegate->dispatch($event);
    }
}
