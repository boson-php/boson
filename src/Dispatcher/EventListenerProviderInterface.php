<?php

declare(strict_types=1);

namespace Boson\Dispatcher;

interface EventListenerProviderInterface
{
    /**
     * Gets event listener of the context with events
     * and intention subscriptions.
     */
    public EventListenerInterface $events { get; }
}
