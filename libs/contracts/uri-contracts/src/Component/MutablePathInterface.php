<?php

declare(strict_types=1);

namespace Boson\Contracts\Uri\Component;

use Boson\Contracts\Uri\Exception\InvalidArgumentExceptionInterface;

interface MutablePathInterface extends PathInterface
{
    public bool $isAbsolute {
        get;
        /**
         * Allows to modify the {@see $isAbsolute} value.
         */
        set;
    }

    public bool $hasTrailingSlash {
        get;
        /**
         * Allows to modify the {@see $hasTrailingSlash} value.
         */
        set;
    }

    /**
     * @param iterable<mixed, \Stringable|non-empty-string> $segments
     *
     * @throws InvalidArgumentExceptionInterface if an invalid path segment is provided
     */
    public function set(iterable $segments): void;
}
