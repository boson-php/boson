<?php

declare(strict_types=1);

namespace Boson\Window\Exception;

use Boson\WebView\Exception\WebViewException;

final class NoParentApplicationException extends WebViewException
{
    public static function becauseNoParentApplication(?\Throwable $previous = null): self
    {
        $message = 'The window cannot be controlled perhaps the parent application was removed (closed) earlier';

        return new self($message, 0, $previous);
    }
}
