<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Internal;

use Boson\WebView\Api\WebComponentsApi\Element\TemplateContainerInterface;

/**
 * @internal this is an internal library class, please do not use it in your code.
 * @psalm-internal Boson\WebView\Api\WebComponentsApi
 */
class ImmutableShadowDomContainer implements TemplateContainerInterface
{
    public string $html {
        get => $this->ctx->get('shadowRoot.innerHTML');
    }

    public string $text {
        get => (string) $this->ctx->get('shadowRoot.textContent');
    }

    /**
     * @param non-empty-string $id
     */
    public function __construct(
        protected ElementInteractor $ctx,
    ) {}
}
