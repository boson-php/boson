<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Exception;

class InvalidPortArgumentException extends InvalidUriComponentArgumentException
{
    public static function getComponentName(): string
    {
        return 'host';
    }
}
