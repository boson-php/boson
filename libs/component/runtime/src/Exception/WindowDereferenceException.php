<?php

declare(strict_types=1);

namespace Boson\Exception;

class WindowDereferenceException extends ApplicationException
{
    public static function becauseNoDefaultWindow(?\Throwable $previous = null): self
    {
        $message = 'There is no default window available, perhaps it was destroyed (closed) earlier';

        return new self($message, 0, $previous);
    }
}
