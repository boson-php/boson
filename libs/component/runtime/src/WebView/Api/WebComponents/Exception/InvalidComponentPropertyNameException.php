<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Exception;

class InvalidComponentPropertyNameException extends WebComponentsApiException
{
    public static function becausePropertyNameIsInvalid(
        string $name,
        string $property,
        ?\Throwable $previous = null,
    ): self {
        $message = \sprintf('The "%s" component "%s" property name is not valid', $name, $property);

        return new self($message, 0, $previous);
    }
}
