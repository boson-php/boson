<?php

declare(strict_types=1);

namespace Boson\Window\Exception;

class WebViewDereferenceException extends WindowException
{
    public static function becauseNoDefaultWebView(?\Throwable $previous = null): self
    {
        $message = 'There is no default webview available, perhaps it was removed (closed) earlier';

        return new self($message, 0, $previous);
    }
}
