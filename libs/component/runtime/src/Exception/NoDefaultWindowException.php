<?php

declare(strict_types=1);

namespace Boson\Exception;

final class NoDefaultWindowException extends ApplicationException
{
    public static function becauseNoDefaultWindow(?\Throwable $previous = null): self
    {
        $message = 'There is no default window available, perhaps it was removed (closed) earlier';

        return new self($message, 0, $previous);
    }
}
