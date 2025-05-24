<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Data\Exception;

class StalledRequestException extends RequestException
{
    public static function becauseRequestIsStalled(string $code, float $timeout, ?\Throwable $previous = null): self
    {
        $message = 'Request "%s" is stalled after %01.2fs of waiting';

        $message = \sprintf($message, \addcslashes($code, '"'), $timeout);

        return new self($message, 0, $previous);
    }
}
