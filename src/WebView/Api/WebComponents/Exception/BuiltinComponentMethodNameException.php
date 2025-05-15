<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Exception;

class BuiltinComponentMethodNameException extends InvalidComponentMethodNameException
{
    public static function becauseMethodNameIsBuiltin(
        string $name,
        string $method,
        ?\Throwable $previous = null,
    ): self {
        $message = \sprintf('The "%s" component method "%s" name is not allowed', $name, $method);

        return new self($message, 0, $previous);
    }
}
