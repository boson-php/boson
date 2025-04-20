<?php

declare(strict_types=1);

namespace Boson\Dispatcher;

use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

interface EventDispatcherInterface extends PsrEventDispatcherInterface
{
    /**
     * Dispatches an event to all registered listeners.
     *
     * @template TArgEvent of object
     *
     * @param TArgEvent $event the event to pass to the event handlers/listeners
     *
     * @return TArgEvent the passed $event MUST be returned
     */
    public function dispatch(object $event): object;
}
