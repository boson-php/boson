<?php

declare(strict_types=1);

namespace Boson\WebView\Exception;

final class NoWebViewApiException extends WebViewApiException
{
    public static function becauseNoWebView(?\Throwable $previous = null): self
    {
        $message = 'A WebView API cannot be controlled perhaps the parent webview was removed (closed) earlier';

        return new self($message, 0, $previous);
    }
}
