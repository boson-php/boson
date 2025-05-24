<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Battery\Exception;

class InsecureBatteryContextException extends BatteryNotAvailableException
{
    public static function becauseContextIsInsecure(?\Throwable $previous = null): self
    {
        $message = 'Battery API requires a secure context (%s) to execute';
        $message = \sprintf($message, 'https://developer.mozilla.org/en-US/docs/Web/Security/Secure_Contexts');

        return new self($message, 0, $previous);
    }
}
