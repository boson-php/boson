<?php

declare(strict_types=1);

namespace Boson\Shared\Id;

use Boson\Shared\ValueObject\StringValueObjectInterface;

/**
 * Representation of all string-like identifiers.
 */
interface StringIdInterface extends
    StringValueObjectInterface,
    IdInterface {}
