<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\Template;

use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentTemplateMetadata;

interface TemplateReaderInterface
{
    /**
     * @param class-string $component
     */
    public function findTemplate(string $component): ?WebComponentTemplateMetadata;
}
