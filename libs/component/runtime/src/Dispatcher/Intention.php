<?php

declare(strict_types=1);

namespace Boson\Dispatcher;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Any hook object is an intent to perform an event.
 *
 * @template TSubject of object
 *
 * @template-extends Event<TSubject>
 */
abstract class Intention extends Event implements StoppableEventInterface
{
    /**
     * Gets the cancellation status of the event.
     */
    public private(set) bool $isCancelled = false;

    /**
     * Cancel the intention.
     */
    public function cancel(): void
    {
        $this->isCancelled = true;
    }
}
