<?php

declare(strict_types=1);

namespace Boson\Window\Exception;

final class NoWindowApiException extends WindowApiException
{
    public static function becauseNoWindow(?\Throwable $previous = null): self
    {
        $message = 'A Window API cannot be controlled perhaps the parent window was removed (closed) earlier';

        return new self($message, 0, $previous);
    }
}
