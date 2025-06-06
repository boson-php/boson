<?php

declare(strict_types=1);

namespace Boson\Shared\ValueObject\Id;

use Boson\Shared\ValueObject\IntValueObjectInterface;

/**
 * Representation of all int-like identifiers.
 *
 * Note: All {@see int} identifiers also supports {@see string}
 *       representation features.
 *
 * @template-covariant T of int = int
 *
 * @template-extends IntValueObjectInterface<T>
 */
interface IntIdInterface extends
    IntValueObjectInterface,
    IdInterface {}
