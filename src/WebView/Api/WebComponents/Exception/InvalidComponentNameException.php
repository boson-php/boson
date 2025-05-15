<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Exception;

class InvalidComponentNameException extends WebComponentsApiException
{
    public static function becauseComponentNameIsInvalid(string $name, ?\Throwable $previous = null): self
    {
        $message = \sprintf('The "%s" component name is not valid', $name);

        return new self($message, 0, $previous);
    }
}
