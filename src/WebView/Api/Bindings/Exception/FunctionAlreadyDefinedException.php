<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Bindings\Exception;

class FunctionAlreadyDefinedException extends BindingsApiException
{
    public static function becauseFunctionAlreadyDefined(string $name, ?\Throwable $previous = null): self
    {
        $message = \sprintf('Cannot redeclare already defined function %s()', $name);

        return new self($message, 0, $previous);
    }
}
