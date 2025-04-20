<?php

declare(strict_types=1);

namespace Boson\Shared\ValueObject;

/**
 * Representation of all value objects that contain {@see string} casting.
 */
interface StringValueObjectInterface extends ValueObjectInterface
{
    /**
     * Gets VO value as PHP {@see string} scalar.
     *
     * @return non-empty-string
     */
    public function toString(): string;
}
