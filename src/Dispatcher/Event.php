<?php

declare(strict_types=1);

namespace Boson\Dispatcher;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Any event object is the result of an action taken.
 *
 * @template TSubject of object
 */
abstract class Event implements StoppableEventInterface, \Stringable
{
    /**
     * Gets the propagation status of the event.
     */
    public private(set) bool $isPropagationStopped = false;

    /**
     * This value is the number of nanoseconds elapsed from the beginning of
     * the time origin until the event was created.
     */
    public readonly int $time;

    public function __construct(
        /**
         * The read-only subject of the {@see Event} class. A reference to
         * the object that sent the event.
         *
         * @var TSubject
         */
        public object $subject,
        ?int $time = null,
    ) {
        echo static::class . "\n";
        $this->time = $time ?? \hrtime(true);
    }

    public function isPropagationStopped(): bool
    {
        return $this->isPropagationStopped;
    }

    /**
     * Stops the propagation of the hook to further event listeners.
     *
     * If multiple event listeners are connected to the same event, no
     * further event listener will be triggered once any trigger
     * calls {@see self::stopPropagation()}.
     */
    public function stopPropagation(): void
    {
        $this->isPropagationStopped = true;
    }

    public function __toString(): string
    {
        return static::class;
    }
}
