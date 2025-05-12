<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\DisconnectMethod;

use Boson\WebView\Api\WebComponentsApi\Attribute\AsDisconnectHandler;

final readonly class AttributeDisconnectMethodReader implements DisconnectMethodReaderInterface
{
    public function __construct(
        private ?DisconnectMethodReaderInterface $delegate = null,
    ) {}

    public function findDisconnectMethod(string $component): ?string
    {
        $reflection = new \ReflectionClass($component);

        foreach ($reflection->getMethods() as $method) {
            foreach ($this->getMethodAttributes($method) as $_) {
                return $method->name;
            }
        }

        return $this->delegate?->findDisconnectMethod($component);
    }

    /**
     * @return list<AsDisconnectHandler>
     */
    private function getMethodAttributes(\ReflectionMethod $reflection): array
    {
        $result = [];

        foreach ($reflection->getAttributes(AsDisconnectHandler::class) as $attribute) {
            $result[] = $attribute->newInstance();
        }

        return $result;
    }
}
