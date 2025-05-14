<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Internal;

use Boson\WebView\Api\WebComponentsApi\Element\TemplateContainerInterface;

class ImmutableTemplateContainer implements TemplateContainerInterface
{
    public string $html {
        get => $this->ctx->get('innerHTML');
    }

    public string $text {
        get => (string) $this->ctx->get('textContent');
    }

    /**
     * @param non-empty-string $id
     */
    public function __construct(
        protected ElementInteractor $ctx,
    ) {}
}
