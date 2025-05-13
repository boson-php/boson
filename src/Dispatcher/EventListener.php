<?php

declare(strict_types=1);

namespace Boson\Dispatcher;

use Boson\Dispatcher\Subscription\CancellableSubscription;
use Boson\Dispatcher\Subscription\CancellableSubscriptionInterface;
use Boson\Dispatcher\Subscription\SubscriptionInterface;
use Boson\Shared\IdValueGenerator\IdValueGeneratorInterface;
use Boson\Shared\IdValueGenerator\PlatformDependentIntValueGenerator;
use Psr\EventDispatcher\StoppableEventInterface;

class EventListener implements EventListenerInterface, EventDispatcherInterface
{
    /**
     * @var array<class-string<object>, array<array-key, callable(object):void>>
     */
    protected array $listeners = [];

    public function __construct(
        /**
         * @var IdValueGeneratorInterface<array-key>
         */
        protected readonly IdValueGeneratorInterface $ids = new PlatformDependentIntValueGenerator(),
    ) {}

    public function getListenersForEvent(object $event): iterable
    {
        if (!isset($this->listeners[$event::class])) {
            return [];
        }

        return $this->listeners[$event::class];
    }

    public function addEventListener(string $event, callable $listener): CancellableSubscriptionInterface
    {
        $subscription = new CancellableSubscription(
            id: $this->ids->nextId(),
            name: $event,
            // @phpstan-ignore-next-line
            canceller: $this->removeEventListener(...),
        );

        // @phpstan-ignore-next-line
        $this->listeners[$event][$subscription->id] = $listener(...);

        return $subscription;
    }

    public function removeEventListener(SubscriptionInterface $subscription): void
    {
        unset($this->listeners[$subscription->name][$subscription->id]);
    }

    public function removeAllEventListenersForEvent(string $event): void
    {
        unset($this->listeners[$event]);
    }

    private function dispatchStoppableEvent(StoppableEventInterface $event): void
    {
        foreach ($this->getListenersForEvent($event) as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            $listener($event);
        }
    }

    public function dispatch(object $event): object
    {
        if ($event instanceof StoppableEventInterface) {
            $this->dispatchStoppableEvent($event);

            return $event;
        }

        foreach ($this->getListenersForEvent($event) as $listener) {
            $listener($event);
        }

        return $event;
    }
}
