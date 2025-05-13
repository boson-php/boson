<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Exception;

class BuiltinComponentNameException extends InvalidComponentNameException
{
    public static function becauseComponentNameIsBuiltin(string $name, ?\Throwable $previous = null): self
    {
        $message = \sprintf('The "%s" component name already registered by the webview', $name);

        return new self($message, 0, $previous);
    }
}
