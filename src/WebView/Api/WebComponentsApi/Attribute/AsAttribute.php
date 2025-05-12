<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class AsAttribute
{
    public function __construct(
        /**
         * @var non-empty-string|null
         */
        public ?string $name = null,
    ) {}
}
