<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\ClassName;

use Boson\WebView\Api\WebComponentsApi\Attribute\AsWebComponent;

final readonly class AttributeClassNameReader implements ClassNameReaderInterface
{
    public function __construct(
        private ClassNameReaderInterface $delegate,
    ) {}

    public function getClassName(string $component): string
    {
        $reflection = new \ReflectionClass($component);

        foreach ($this->getClassAttributes($reflection) as $attribute) {
            if ($attribute->class !== null) {
                return $attribute->class;
            }
        }

        return $this->delegate->getClassName($component);
    }

    /**
     * @param \ReflectionClass<object> $reflection
     *
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
