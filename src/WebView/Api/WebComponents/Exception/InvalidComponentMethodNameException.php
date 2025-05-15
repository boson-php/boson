<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Exception;

class InvalidComponentMethodNameException extends WebComponentsApiException
{
    public static function becauseMethodNameIsInvalid(
        string $name,
        string $method,
        ?\Throwable $previous = null,
    ): self {
        $message = \sprintf('The "%s" component "%s" method name is not valid', $name, $method);

        return new self($message, 0, $previous);
    }
}
