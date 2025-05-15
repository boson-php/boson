<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Battery\Exception;

class BatteryNotReadyException extends BatteryApiException
{
    public static function becauseBatteryNotReady(?\Throwable $previous = null): self
    {
        $message = 'Obtaining battery information is only available after the document is ready';

        return new self($message, 0, $previous);
    }
}
