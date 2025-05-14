<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Internal;

use Boson\WebView\Api\WebComponentsApi\Element\TemplateContainerInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Api\WebComponentsApi
 */
class ImmutableShadowDomContainer implements TemplateContainerInterface
{
    public string $html {
        /** @phpstan-ignore-next-line : A shadowRoot.innerHTML will return string */
        get => (string) $this->ctx->get('shadowRoot.innerHTML');
    }

    public string $text {
        /** @phpstan-ignore-next-line : A shadowRoot.textContent will return string|null */
        get => (string) $this->ctx->get('shadowRoot.textContent');
    }

    public function __construct(
        protected ElementInteractor $ctx,
    ) {}
}
