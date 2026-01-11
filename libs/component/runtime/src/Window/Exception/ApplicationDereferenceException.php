<?php

declare(strict_types=1);

namespace Boson\Window\Exception;

class ApplicationDereferenceException extends WindowException
{
    public static function becauseNoParentApplication(?\Throwable $previous = null): self
    {
        $message = 'The parent application cannot be accessed because it was previously destroyed (closed)';

        return new self($message, 0, $previous);
    }
}
