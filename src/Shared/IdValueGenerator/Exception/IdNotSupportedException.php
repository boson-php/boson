<?php

declare(strict_types=1);

namespace Boson\Shared\IdValueGenerator\Exception;

final class IdNotSupportedException extends IdException
{
    /**
     * @param non-empty-string $expected
     * @param non-empty-string $actual
     */
    public static function becauseInvalidPlatform(string $expected, string $actual, ?\Throwable $previous = null): self
    {
        $message = 'The current platform does not support %s identifiers, only %s is allowed';
        $message = \sprintf($message, $expected, $actual);

        return new self($message, 0x01, $previous);
    }
}
