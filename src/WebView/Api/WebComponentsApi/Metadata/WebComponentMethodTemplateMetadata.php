<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata;

final readonly class WebComponentMethodTemplateMetadata extends WebComponentTemplateMetadata
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public string $name,
    ) {}
}
