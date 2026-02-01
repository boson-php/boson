<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Exception;

/**
 * @phpstan-consistent-constructor
 */
class InvalidPathArgumentException extends InvalidUriComponentArgumentException
{
    public static function getComponentName(): string
    {
        return 'path';
    }
}
