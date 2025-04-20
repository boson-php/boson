<?php

declare(strict_types=1);

namespace Boson\Dispatcher\Subscription;

/**
 * @template T of object
 * @template-extends Subscription<T>
 * @template-implements CancellableSubscriptionInterface<T>
 */
final class CancellableSubscription extends Subscription implements CancellableSubscriptionInterface
{
    public private(set) bool $isCancelled = false;

    /**
     * @var \Closure(SubscriptionInterface<T>):void
     */
    private readonly \Closure $canceller;

    /**
     * @param array-key $id
     * @param class-string<T> $name
     * @param callable(SubscriptionInterface<T>):void $canceller
     */
    public function __construct(
        int|string $id,
        string $name,
        callable $canceller,
    ) {
        parent::__construct($id, $name);

        $this->canceller = $canceller(...);
    }

    public function cancel(): void
    {
        if ($this->isCancelled === true) {
            return;
        }

        ($this->canceller)($this);
        $this->isCancelled = true;
    }
}
