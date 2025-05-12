<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\TagName;

use Boson\WebView\Api\WebComponentsApi\Attribute\AsWebComponent;

final readonly class AttributeTagNameReader implements TagNameReaderInterface
{
    public function __construct(
        private TagNameReaderInterface $delegate,
    ) {}

    public function getTagName(string $component): string
    {
        try {
            $reflection = new \ReflectionClass($component);
        } catch (\ReflectionException) {
            return $this->delegate->getTagName($component);
        }

        foreach ($this->getClassAttributes($reflection) as $attribute) {
            if ($attribute->tag !== null) {
                return \strtolower($attribute->tag);
            }
        }

        return $this->delegate->getTagName($component);
    }

    /**
     * @return list<AsWebComponent>
     */
    private function getClassAttributes(\ReflectionClass $reflection): array
    {
        $result = [];

        foreach ($reflection->getAttributes(AsWebComponent::class) as $attribute) {
            $result[] = $attribute->newInstance();
        }

        return $result;
    }
}
