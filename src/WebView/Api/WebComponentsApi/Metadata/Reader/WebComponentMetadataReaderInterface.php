<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader;

use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentMetadata;

interface WebComponentMetadataReaderInterface
{
    /**
     * Gets component metadata
     *
     * @param class-string $component
     */
    public function getMetadata(string $component): WebComponentMetadata;
}
