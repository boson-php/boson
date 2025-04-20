<?php

declare(strict_types=1);

namespace Boson\Dispatcher\Subscription;

/**
 * @template TEvent of object = object
 */
interface SubscriptionInterface
{
    /**
     * An identifier of the subscription.
     *
     * @var array-key
     */
    public int|string $id { get; }

    /**
     * @var class-string<TEvent>
     */
    public string $name { get; }
}
