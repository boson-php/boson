<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\ConnectMethod;

use Boson\WebView\Api\WebComponentsApi\Attribute\AsConnectHandler;

final readonly class AttributeConnectMethodReader implements ConnectMethodReaderInterface
{
    public function __construct(
        private ?ConnectMethodReaderInterface $delegate = null,
    ) {}

    public function findConnectMethod(string $component): ?string
    {
        $reflection = new \ReflectionClass($component);

        foreach ($reflection->getMethods() as $method) {
            foreach ($this->getMethodAttributes($method) as $_) {
                return $method->name;
            }
        }

        return $this->delegate?->findConnectMethod($component);
    }

    /**
     * @return list<AsConnectHandler>
     */
    private function getMethodAttributes(\ReflectionMethod $reflection): array
    {
        $result = [];

        foreach ($reflection->getAttributes(AsConnectHandler::class) as $attribute) {
            $result[] = $attribute->newInstance();
        }

        return $result;
    }
}
