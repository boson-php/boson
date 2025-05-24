<?php

declare(strict_types=1);

namespace Boson\Shared\ValueObject;

interface ValueObjectInterface extends \Stringable
{
    /**
     * Returns {@see true} if the object is equal to the given
     * object and {@see false} otherwise.
     */
    public function equals(mixed $object): bool;

    /**
     * Returns a string representation of the object.
     */
    public function __toString(): string;
}
