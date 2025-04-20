<?php

declare(strict_types=1);

namespace Boson\WebView\Binding\Exception;

final class InvalidFunctionException extends WebViewBindingException
{
    public static function becauseFunctionNotDefined(string $name, ?\Throwable $previous = null): self
    {
        $message = \sprintf('RPC function "%s" has not been defined', $name);

        return new self($message, 0, $previous);
    }
}
