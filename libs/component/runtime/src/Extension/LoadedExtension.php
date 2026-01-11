<?php

declare(strict_types=1);

namespace Boson\Extension;

use Boson\Contracts\EventListener\Subscription\CancellableSubscriptionInterface;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\Event;
use Boson\Dispatcher\EventListener;
use Boson\Dispatcher\Intention;
use Boson\Shared\StructPointerId;

/**
 * @template TContext of IdentifiableInterface<StructPointerId>
 */
abstract class LoadedExtension
{
    public function __construct(
        protected readonly EventListener $listener,
    ) {}

    /**
     * @template TArgEvent of object
     *
     * @param class-string<TArgEvent> $event the event (class) name
     * @param callable(TArgEvent):void $then the listener callback
     *
     * @return CancellableSubscriptionInterface<TArgEvent>
     */
    protected function listen(string $event, callable $then): CancellableSubscriptionInterface
    {
        return $this->listener->addEventListener($event, $then);
    }

    /**
     * Dispatch intention instance and returns {@see false} in
     * case of intention has been cancelled.
     *
     * @param Intention<TContext> $intention
     */
    protected function intent(Intention $intention): bool
    {
        $this->listener->dispatch($intention);

        return $intention->isCancelled === false;
    }

    /**
     * Dispatch immutable event.
     *
     * @param Event<TContext> $event
     */
    protected function dispatch(Event $event): void
    {
        $this->listener->dispatch($event);
    }
}
