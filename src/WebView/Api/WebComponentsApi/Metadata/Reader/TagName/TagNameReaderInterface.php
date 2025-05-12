<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\TagName;

interface TagNameReaderInterface
{
    /**
     * @param class-string $component
     * @return non-empty-lowercase-string
     */
    public function getTagName(string $component): string;
}
