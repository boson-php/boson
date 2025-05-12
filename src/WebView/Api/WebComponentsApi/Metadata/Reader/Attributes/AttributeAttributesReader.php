<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\Attributes;

use Boson\WebView\Api\WebComponentsApi\Attribute\AsAttribute;
use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentAttributeMetadata;

final readonly class AttributeAttributesReader implements AttributesReaderInterface
{
    public function getAttributes(string $component): array
    {
        try {
            $reflection = new \ReflectionClass($component);
        } catch (\ReflectionException) {
            return [];
        }

        $result = [];

        foreach ($reflection->getProperties() as $property) {
            foreach ($this->getPropertyAttributes($property) as $attribute) {
                $result[] = new WebComponentAttributeMetadata(
                    property: $property->name,
                    attribute: $attribute->name ?? $property->name,
                );
            }
        }

        return $result;
    }

    /**
     * @return list<AsAttribute>
     */
    private function getPropertyAttributes(\ReflectionProperty $reflection): array
    {
        $result = [];

        foreach ($reflection->getAttributes(AsAttribute::class) as $attribute) {
            $result[] = $attribute->newInstance();
        }

        return $result;
    }
}
