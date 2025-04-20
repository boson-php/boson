<?php

declare(strict_types=1);

namespace Boson\Shared\IdValueGenerator;

enum OverflowBehaviour
{
    /**
     * In case of overflow of identifiers, reset the value to zero.
     */
    case Reset;

    /**
     * In case of overflow of a valid set of identifiers, throw an exception.
     */
    case Exception;
}
