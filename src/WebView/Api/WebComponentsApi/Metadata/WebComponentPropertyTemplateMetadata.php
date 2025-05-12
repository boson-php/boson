<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata;

final readonly class WebComponentPropertyTemplateMetadata extends WebComponentTemplateMetadata
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public string $name,
    ) {}
}
