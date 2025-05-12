<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Exception;

class ComponentAlreadyDefinedExceptionWeb extends WebComponentsApiException
{
    public static function becauseComponentAlreadyDefined(
        string $tag,
        string $component,
        ?\Throwable $previous = null,
    ): self {
        $message = \sprintf('Cannot redeclare already defined component <%s /> (%s)', $tag, $component);

        return new self($message, 0, $previous);
    }
}
