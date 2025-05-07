<?php

declare(strict_types=1);

namespace Boson\WebView\Api\FunctionsApi\Exception;

class FunctionNotDefinedException extends FunctionsApiException
{
    public static function becauseFunctionNotDefined(string $name, ?\Throwable $previous = null): self
    {
        $message = \sprintf('Cannot remove undefined function %s()', $name);

        return new self($message, 0, $previous);
    }
}
