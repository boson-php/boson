<?php

declare(strict_types=1);

namespace Boson\Window\Exception;

class WindowApiDereferenceException extends WindowApiException
{
    public static function becauseNoWindow(?\Throwable $previous = null): self
    {
        $message = 'The window cannot be accessed because it was previously destroyed (closed)';

        return new self($message, 0, $previous);
    }
}
