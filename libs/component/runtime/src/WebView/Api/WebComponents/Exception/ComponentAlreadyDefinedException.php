<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Exception;

class ComponentAlreadyDefinedException extends WebComponentsApiException
{
    public static function becauseComponentAlreadyDefined(
        string $name,
        string $component,
        ?\Throwable $previous = null,
    ): self {
        $message = \sprintf('Cannot redeclare already defined component <%s /> (%s)', $name, $component);

        return new self($message, 0, $previous);
    }
}
