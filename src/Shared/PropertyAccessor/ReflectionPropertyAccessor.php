<?php

declare(strict_types=1);

namespace Boson\Shared\PropertyAccessor;

final readonly class ReflectionPropertyAccessor implements PropertyAccessorInterface
{
    /**
     * @param non-empty-string $property
     *
     * @throws \ReflectionException
     */
    private function getPropertyForGet(object $object, string $property): \ReflectionProperty
    {
        return new \ReflectionProperty($object, $property);
    }

    /**
     * @param non-empty-string $property
     *
     * @throws \ReflectionException
     */
    private function getPropertyForSet(object $object, string $property): \ReflectionProperty
    {
        $reflection = new \ReflectionProperty($object, $property);

        $context = $reflection->getDeclaringClass();

        return $context->getProperty($property);
    }

    public function getValue(object $object, string $property): mixed
    {
        try {
            $reflection = $this->getPropertyForGet($object, $property);

            return $reflection->getValue($object);
        } catch (\ReflectionException) {
            return null;
        }
    }

    public function isReadable(object $object, string $property): bool
    {
        if (!\property_exists($object, $property)) {
            return false;
        }

        try {
            return $this->isReadableUsingHooks($object, $property);
        } catch (\ReflectionException) {
            return false;
        }
    }

    /**
     * @param non-empty-string $property
     *
     * @throws \ReflectionException
     */
    private function isReadableUsingHooks(object $object, string $property): bool
    {
        $reflection = $this->getPropertyForSet($object, $property);

        return $reflection->getHook(\PropertyHookType::Get) !== null
            || $reflection->getHook(\PropertyHookType::Set) === null;
    }

    public function setValue(object $object, string $property, mixed $value): void
    {
        try {
            $reflection = $this->getPropertyForSet($object, $property);

            $reflection->setValue($object, $value);
        } catch (\ReflectionException) {
            return;
        }
    }

    public function isWritable(object $object, string $property): bool
    {
        try {
            return $this->isWritableUsingHooks($object, $property);
        } catch (\ReflectionException) {
            return false;
        }
    }

    /**
     * @param non-empty-string $property
     *
     * @throws \ReflectionException
     */
    private function isWritableUsingHooks(object $object, string $property): bool
    {
        $reflection = $this->getPropertyForSet($object, $property);

        return $reflection->getHook(\PropertyHookType::Get) === null
            || $reflection->getHook(\PropertyHookType::Set) !== null;
    }
}
