<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Data\Exception;

class ApplicationNotRunningException extends RequestException
{
    public static function becauseApplicationNotRunning(string $code, ?\Throwable $previous = null): self
    {
        $message = 'Request "%s" could not be processed because application is not running';
        $message = \sprintf($message, \addcslashes($code, '"'));

        return new self($message, 0, $previous);
    }
}
