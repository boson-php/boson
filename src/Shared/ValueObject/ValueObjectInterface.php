<?php

declare(strict_types=1);

namespace Boson\Shared\ValueObject;

interface ValueObjectInterface extends \Stringable
{
    /**
     * Returns {@see true} if the object is equal to the given
     * object and {@see false} otherwise.
     */
    public function equals(self $object): bool;

    /**
     * Returns a string representation of the object.
     *
     * @return non-empty-string
     */
    public function __toString(): string;
}
