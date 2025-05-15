<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Battery\Exception;

class BatteryNotAvailableException extends BatteryApiException
{
    public static function becauseBatteryNotAvailable(?\Throwable $previous = null): self
    {
        $message = 'The current runtime does not support getting battery information';

        return new self($message, 0, $previous);
    }
}
