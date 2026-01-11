<?php

declare(strict_types=1);

namespace Boson\WebView\Exception;

final class NoParentWindowException extends WebViewException
{
    public static function becauseNoParentWindow(?\Throwable $previous = null): self
    {
        $message = 'The webview cannot be controlled perhaps the parent window was removed (closed) earlier';

        return new self($message, 0, $previous);
    }
}
