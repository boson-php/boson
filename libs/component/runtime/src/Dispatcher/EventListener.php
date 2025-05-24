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

    /**
     * @var array<class-string, class-string>
     */
    private static array $aliases = [];

    public function __construct(
        /**
         * @var IdValueGeneratorInterface<array-key>
         */
        protected readonly IdValueGeneratorInterface $ids = new PlatformDependentIntValueGenerator(),
    ) {}

    /**
     * @param class-string $class
     * @param class-string $willBeDefinedAs
     */
    public static function addEventAlias(string $class, string $willBeDefinedAs): void
    {
        self::$aliases[$class] = $willBeDefinedAs;
    }

    public function getListenersForEvent(object $event): iterable
    {
        if (!isset($this->listeners[$event::class])) {
            return [];
        }

        return $this->listeners[$event::class];
    }

    public function addEventListener(string $event, callable $listener): CancellableSubscriptionInterface
    {
        $event = self::$aliases[$event] ?? $event;

        $subscription = new CancellableSubscription(
            id: $this->ids->nextId(),
            name: $event,
            /** @phpstan-ignore-next-line */
            canceller: $this->removeEventListener(...),
        );

        /** @phpstan-ignore-next-line */
        $this->listeners[$event][$subscription->id] = $listener(...);

        /** @phpstan-ignore-next-line */
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
