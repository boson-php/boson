<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Exception;

/**
 * @phpstan-consistent-constructor
 */
class InvalidHostArgumentException extends InvalidUriComponentArgumentException
{
    public static function getComponentName(): string
    {
        return 'host';
    }
}
