<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\Attributes;

use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentAttributeMetadata;

interface AttributesReaderInterface
{
    /**
     * @param class-string $component
     *
     * @return list<WebComponentAttributeMetadata>
     */
    public function getAttributes(string $component): array;
}
