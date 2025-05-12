<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata;

use JetBrains\PhpStorm\Language;

final readonly class WebComponentHtmlTemplateMetadata extends WebComponentTemplateMetadata
{
    public function __construct(
        #[Language('HTML')]
        public string $html,
    ) {}
}
