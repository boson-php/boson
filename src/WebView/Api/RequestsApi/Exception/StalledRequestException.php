<?php

declare(strict_types=1);

namespace Boson\WebView\Api\RequestsApi\Exception;

class StalledRequestException extends RequestException
{
    public static function becauseRequestIsStalled(string $code, float $timeout, ?\Throwable $previous = null): self
    {
        $message = 'Request "%s" is stalled after %01.2fs of waiting;'
            . ' This is most likely due to the fact that the request contains'
            . ' incorrect JavaScript code (syntax error)';

        $message = \sprintf($message, \addcslashes($code, '"'), $timeout);

        return new self($message, 0, $previous);
    }
}
