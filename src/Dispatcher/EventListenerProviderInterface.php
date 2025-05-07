<?php

declare(strict_types=1);

namespace Boson\Dispatcher;

use Boson\Dispatcher\Subscription\CancellableSubscriptionInterface;

interface EventListenerProviderInterface
{
    /**
     * Gets event listener of the context with events
     * and intention subscriptions.
     */
    public EventListenerInterface $events { get; }

    /**
     * Provides a simplified interface for subscribing to events.
     *
     * Option to register listener without a name:
     * ```
     * $ctx->on(function(ExampleEvent $e): void {
     *     var_dump($e);
     * });
     * ```
     *
     * Option to register listener with name:
     * ```
     * $ctx->on(ExampleEvent::class, function(ExampleEvent $e): void {
     *     var_dump($e);
     * });
     *
     * // or
     *
     * $ctx->on(ExampleEvent::class, function(): void {
     *     var_dump('ExampleEvent fired!');
     * });
     * ```
     *
     * @template TArgEvent of object
     *
     * @param class-string<TArgEvent>|\Closure(TArgEvent):void $eventOrListener
     * @param \Closure(TArgEvent):void|null $listener
     *
     * @return CancellableSubscriptionInterface<TArgEvent>
     */
    public function on(\Closure|string $eventOrListener, ?\Closure $listener = null): CancellableSubscriptionInterface;
}
