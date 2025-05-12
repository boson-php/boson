<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\Template;

use Boson\WebView\Api\WebComponentsApi\Attribute\AsTemplate;
use Boson\WebView\Api\WebComponentsApi\Attribute\AsWebComponent;
use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentHtmlTemplateMetadata;
use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentMethodTemplateMetadata;
use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentPropertyTemplateMetadata;
use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentTemplateMetadata;

final readonly class AttributeTemplateReader implements TemplateReaderInterface
{
    public function __construct(
        private ?TemplateReaderInterface $delegate = null,
    ) {}

    public function findTemplate(string $component): ?WebComponentTemplateMetadata
    {
        $reflection = new \ReflectionClass($component);

        foreach ($this->getClassAttributes($reflection) as $attribute) {
            if ($attribute->template === null) {
                continue;
            }

            return new WebComponentHtmlTemplateMetadata(
                html: $attribute->template,
            );
        }

        foreach ($reflection->getProperties() as $property) {
            if ($property->name === '') {
                continue;
            }

            foreach ($this->getPropertyAttributes($property) as $_) {
                return new WebComponentPropertyTemplateMetadata(
                    name: $property->name,
                );
            }
        }

        foreach ($reflection->getMethods() as $method) {
            if ($method->name === '') {
                continue;
            }

            foreach ($this->getMethodAttributes($method) as $_) {
                return new WebComponentMethodTemplateMetadata(
                    name: $method->name,
                );
            }
        }

        return $this->delegate?->findTemplate($component);
    }

    /**
     * @return list<AsTemplate>
     */
    private function getMethodAttributes(\ReflectionMethod $reflection): array
    {
        $result = [];

        foreach ($reflection->getAttributes(AsTemplate::class) as $attribute) {
            $result[] = $attribute->newInstance();
        }

        return $result;
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

    /**
     * @return list<AsTemplate>
     */
    private function getPropertyAttributes(\ReflectionProperty $reflection): array
    {
        $result = [];

        foreach ($reflection->getAttributes(AsTemplate::class) as $attribute) {
            $result[] = $attribute->newInstance();
        }

        return $result;
    }
}
