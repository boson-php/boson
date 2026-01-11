<?php

declare(strict_types=1);

namespace Boson\Component\WeakType\Internal;

/**
 * @template TReference of object
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Component\WeakType
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
