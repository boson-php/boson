<?php

declare(strict_types=1);

namespace Boson\Component\WeakType;

/**
 * @template TReference of object
 */
final readonly class ReferenceReleaseCallback
{
    public function __construct(
        /**
         * @var TReference
         */
        public object $reference,
        /**
         * @var \Closure(TReference):void
         */
        private \Closure $onRelease,
    ) {}

    public function __destruct()
    {
        ($this->onRelease)($this->reference);
    }
}
