<?php

declare(strict_types=1);

namespace Boson\Shared\GarbageCollector;

/**
 * @template TEntry of object
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson
 */
final readonly class DestructorObserver
{
    public function __construct(
        /**
         * @var TEntry
         */
        public object $entry,
        /**
         * @var \Closure(TEntry):void
         */
        private \Closure $onRelease,
    ) {}

    public function __destruct()
    {
        ($this->onRelease)($this->entry);
    }
}
