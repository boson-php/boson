<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata;

final readonly class WebComponentAttributeMetadata
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public string $property,
        /**
         * @var non-empty-string
         */
        public string $attribute,
    ) {}
}
