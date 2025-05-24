<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Exception;

class BuiltinComponentNameException extends InvalidComponentNameException
{
    public static function becauseComponentNameIsBuiltin(string $name, ?\Throwable $previous = null): self
    {
        $message = \sprintf('The "%s" component name is not allowed', $name);

        return new self($message, 0, $previous);
    }
}
