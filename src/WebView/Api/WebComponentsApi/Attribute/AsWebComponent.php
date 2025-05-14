<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class AsWebComponent
{
    public function __construct(
        /**
         * @var non-empty-string|null
         */
        public ?string $className = null,
        public ?string $template = null,
    ) {}
}
