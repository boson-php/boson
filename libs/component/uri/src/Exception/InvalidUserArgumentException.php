<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Exception;

class InvalidUserArgumentException extends InvalidUriComponentArgumentException
{
    public static function getComponentName(): string
    {
        return 'user';
    }
}
