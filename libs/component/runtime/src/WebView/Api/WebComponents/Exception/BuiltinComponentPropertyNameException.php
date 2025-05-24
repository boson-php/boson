<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Exception;

class BuiltinComponentPropertyNameException extends InvalidComponentPropertyNameException
{
    public static function becausePropertyNameIsBuiltin(
        string $name,
        string $property,
        ?\Throwable $previous = null,
    ): self {
        $message = \sprintf('The "%s" component property "%s" name is not allowed', $name, $property);

        return new self($message, 0, $previous);
    }
}
