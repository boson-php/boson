<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\ClassName;

final readonly class NativeClassNameReader implements ClassNameReaderInterface
{
    public function getClassName(string $component): string
    {
        $formatted = \trim($component, '\\');

        return \str_replace('\\', '_', $formatted);
    }
}
