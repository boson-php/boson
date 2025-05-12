<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
final readonly class AsTemplate
{
    public function __construct(
        public ?string $template = null
    ) {}
}
