<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\ClassName;

interface ClassNameReaderInterface
{
    /**
     * @param class-string $component
     * @return non-empty-string
     */
    public function getClassName(string $component): string;
}
