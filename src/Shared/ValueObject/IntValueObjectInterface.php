<?php

declare(strict_types=1);

namespace Boson\Shared\ValueObject;

/**
 * Representation of all value objects that contain {@see int} casting.
 *
 * Note: All {@see int} identifiers also supports {@see int}
 *       representation features.
 *
 * @template-covariant T of int = int
 */
interface IntValueObjectInterface extends StringValueObjectInterface
{
    /**
     * Gets VO value as PHP {@see int} scalar.
     *
     * @return T
     */
    public function toInteger(): int;
}
