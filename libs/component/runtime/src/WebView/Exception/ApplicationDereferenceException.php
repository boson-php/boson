<?php

declare(strict_types=1);

namespace Boson\WebView\Exception;

class ApplicationDereferenceException extends WebViewException
{
    public static function becauseNoParentApplication(?\Throwable $previous = null): self
    {
        $message = 'The parent application cannot be accessed because it was previously destroyed (closed)';

        return new self($message, 0, $previous);
    }
}
