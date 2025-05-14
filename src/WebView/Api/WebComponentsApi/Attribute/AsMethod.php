<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
final readonly class AsMethod
{
    public function __construct(
        /**
         * @var non-empty-string|null
         */
        public ?string $name = null,
        public bool $renderAfterCall = false,
    ) {}
}
