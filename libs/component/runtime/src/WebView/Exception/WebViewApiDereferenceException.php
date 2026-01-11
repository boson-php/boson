<?php

declare(strict_types=1);

namespace Boson\WebView\Exception;

class WebViewApiDereferenceException extends WebViewApiException
{
    public static function becauseNoWebView(?\Throwable $previous = null): self
    {
        $message = 'The webview cannot be accessed because it was previously destroyed (closed)';

        return new self($message, 0, $previous);
    }
}
