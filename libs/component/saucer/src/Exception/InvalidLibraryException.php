<?php

declare(strict_types=1);

namespace Boson\Component\Saucer\Exception;

class InvalidLibraryException extends SaucerException
{
    public static function becauseFileNotFound(string $library, ?\Throwable $previous = null): self
    {
        $message = \sprintf('Library at path "%s" does not exists', $library);

        return new self($message, 0, $previous);
    }
}
