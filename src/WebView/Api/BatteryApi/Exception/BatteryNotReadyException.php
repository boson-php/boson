<?php

declare(strict_types=1);

namespace Boson\WebView\Api\BatteryApi\Exception;

use Boson\WebView\WebViewState;

class BatteryNotReadyException extends BatteryApiException
{
    public static function becauseBatteryNotReady(WebViewState $state, ?\Throwable $previous = null): self
    {
        $message = 'Obtaining battery information is only available after '
            . 'the document is ready, but currently document in [%s] state';

        return new self(\sprintf($message, $state->name), 0, $previous);
    }
}
