<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Exception;

/**
 * @phpstan-consistent-constructor
 */
class InvalidQueryNameArgumentException extends
    InvalidQueryArgumentException
{
    public static function getComponentName(): string
    {
        return 'query parameter name';
    }
}
