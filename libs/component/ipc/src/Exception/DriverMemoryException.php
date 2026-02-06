<?php

declare(strict_types=1);

namespace Boson\Component\Ipc\Exception;

class DriverMemoryException extends DriverException
{
    final public const int ERROR_CODE_ALLOCATION_FAILED = 0x01;
    final public const int ERROR_CODE_ALLOCATION_RESTRICTED = 0x02;

    public static function becauseAllocationFailed(int $current, int $expected, ?\Throwable $prev = null): self
    {
        $diff = $expected - $current;

        $template = $diff > 0
            ? 'Unable to allocate %d bytes (%d total)'
            : 'Unable to free %d bytes (%d total)';

        return new self(\sprintf($template, \abs($diff), $expected), self::ERROR_CODE_ALLOCATION_FAILED, $prev);
    }

    public static function becauseAllocationNotAllowed(?\Throwable $prev = null): self
    {
        $message = 'Dynamic memory allocation is not allowed';

        return new self($message, self::ERROR_CODE_ALLOCATION_RESTRICTED, $prev);
    }
}
