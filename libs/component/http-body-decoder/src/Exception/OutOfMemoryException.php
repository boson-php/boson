<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body\Exception;

final class OutOfMemoryException extends BodyDecoderException
{
    /**
     * @param int<0, max> $actual
     * @param int<0, max> $supported
     */
    public static function becauseMemoryLimitOverflow(int $actual, int $supported, ?\Throwable $prev = null): self
    {
        $message = \sprintf('Payload size of %d bytes exceeds available %d bytes', $actual, $supported);

        return new self($message, 0, $prev);
    }
}
