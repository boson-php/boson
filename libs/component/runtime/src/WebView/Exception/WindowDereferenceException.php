<?php

declare(strict_types=1);

namespace Boson\WebView\Exception;

class WindowDereferenceException extends WebViewException
{
    public static function becauseNoParentWindow(?\Throwable $previous = null): self
    {
        $message = 'The parent window cannot be accessed because it was previously destroyed (closed)';

        return new self($message, 0, $previous);
    }
}
