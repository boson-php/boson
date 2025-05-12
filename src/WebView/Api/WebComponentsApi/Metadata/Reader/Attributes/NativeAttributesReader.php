<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\Attributes;

use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentAttributeMetadata;

final readonly class NativeAttributesReader implements AttributesReaderInterface
{
    public function getAttributes(string $component): array
    {
        try {
            $reflection = new \ReflectionClass($component);
        } catch (\ReflectionException) {
            return [];
        }

        $result = [];

        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $result[] = new WebComponentAttributeMetadata(
                property: $property->name,
                attribute: $property->name,
            );
        }

        return $result;
    }
}
