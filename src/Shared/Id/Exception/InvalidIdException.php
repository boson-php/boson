<?php

declare(strict_types=1);

namespace Boson\Shared\Id\Exception;

final class InvalidIdException extends IdException
{
    public static function becauseTypeIsInvalid(mixed $value, ?\Throwable $prev = null): self
    {
        $message = 'The value of type %s is not a valid identifier';
        $message = \sprintf($message, \get_debug_type($value));

        return new self($message, 0, $prev);
    }

    public static function becauseValueIsInvalid(string $expected, mixed $value, ?\Throwable $prev = null): self
    {
        $message = 'The value %s must be a %s, but invalid identifier given';
        $message = \sprintf($message, self::valueToString($value), $expected);

        return new self($message, 0, $prev);
    }

    private static function valueToString(mixed $value): string
    {
        if (\is_scalar($value)) {
            return \var_export($value, true);
        }

        return \get_debug_type($value);
    }
}
