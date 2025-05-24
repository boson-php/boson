<?php

declare(strict_types=1);

namespace Boson\Dispatcher;

use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

final class DelegateEventListener extends EventListener
{
    public function __construct(
        private readonly PsrEventDispatcherInterface $delegate,
    ) {
        parent::__construct();
    }

    public function dispatch(object $event): object
    {
        $this->delegate->dispatch(parent::dispatch($event));

        return $event;
    }
}
