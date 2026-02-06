<?php

declare(strict_types=1);

namespace Boson\Component\Ipc\Exception;

class DriverReadException extends DriverException
{
    public static function becauseReadFailed(int $bytes, int $offset, ?\Throwable $prev= null): self
    {
        $template = 'Could not read %d bytes from memory at offset %d';

        return new self(\sprintf($template, $bytes, $offset), 0, $prev);
    }
}
