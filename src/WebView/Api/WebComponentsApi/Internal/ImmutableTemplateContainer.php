<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Internal;

use Boson\WebView\Api\WebComponentsApi\Element\TemplateContainerInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Api\WebComponentsApi
 */
class ImmutableTemplateContainer implements TemplateContainerInterface
{
    public string $html {
        /** @phpstan-ignore-next-line : An innerHTML will return string */
        get => (string) $this->ctx->get('innerHTML');
    }

    public string $text {
        /** @phpstan-ignore-next-line : A textContent will return string|null */
        get => (string) $this->ctx->get('textContent');
    }

    public function __construct(
        protected ElementInteractor $ctx,
    ) {}
}
