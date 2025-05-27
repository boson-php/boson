<?php

declare(strict_types=1);

namespace Boson\Dispatcher\Subscription;

/**
 * @template TEvent of object = object
 *
 * @template-extends SubscriptionInterface<TEvent>
 */
interface CancellableSubscriptionInterface extends SubscriptionInterface
{
    /**
     * Returns {@see true} in case of the event is
     * cancelled, {@see false} otherwise.
     */
    public bool $isCancelled { get; }

    /**
     * Cancel the event listener.
     */
    public function cancel(): void;
}
