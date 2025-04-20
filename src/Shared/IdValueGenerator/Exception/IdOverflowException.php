<?php

declare(strict_types=1);

namespace Boson\Shared\IdValueGenerator\Exception;

final class IdOverflowException extends IdException
{
    /**
     * @param non-empty-string $max
     */
    public static function becauseMaxValueOverflows(string $max, ?\Throwable $previous = null): self
    {
        $message = 'Cannot create a new ID because the ID has reached its maximum value %s';

        return new self(\sprintf($message, $max), 0x01, $previous);
    }

    /**
     * @param class-string $class
     * @param non-empty-string $max
     */
    public static function becauseClassOverflows(string $class, string $max, ?\Throwable $previous = null): self
    {
        return self::becauseMaxValueOverflows(\sprintf('%s(%s)', $class, $max), $previous);
    }
}
