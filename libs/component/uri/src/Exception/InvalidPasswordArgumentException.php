<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Exception;

/**
 * @phpstan-consistent-constructor
 */
class InvalidPasswordArgumentException extends InvalidUriComponentArgumentException
{
    public static function getComponentName(): string
    {
        return 'password';
    }

    public static function becauseUserNotDefined(?\Throwable $prev = null): self
    {
        $message = \vsprintf('Cannot update URI %s without %s', [
            static::getComponentName(),
            InvalidUserArgumentException::getComponentName(),
        ]);

        return new self($message, 0, $prev);
    }
}
