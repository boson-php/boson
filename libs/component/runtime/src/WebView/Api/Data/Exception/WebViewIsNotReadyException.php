<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Data\Exception;

class WebViewIsNotReadyException extends RequestException
{
    public static function becauseWebViewIsNotReady(string $code, ?\Throwable $previous = null): self
    {
        $message = 'Request "%s" could not be processed because webview is in navigating state';
        $message = \sprintf($message, \addcslashes($code, '"'));

        return new self($message, 0, $previous);
    }
}
