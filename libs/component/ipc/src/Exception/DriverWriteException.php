<?php

declare(strict_types=1);

namespace Boson\Component\Ipc\Exception;

class DriverWriteException extends DriverException
{
    public static function becauseWriteFailed(int $bytes, int $offset, ?\Throwable $prev = null): self
    {
        $template = 'Could not write %d bytes to memory at offset %d';

        return new self(\sprintf($template, $bytes, $offset), 0, $prev);
    }
}
