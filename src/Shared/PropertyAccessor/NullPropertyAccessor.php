<?php

declare(strict_types=1);

namespace Boson\Shared\PropertyAccessor;

final readonly class NullPropertyAccessor implements PropertyAccessorInterface
{
    public function getValue(object $object, string $property): mixed
    {
        throw new \LogicException(\sprintf(
            'The %s::$%s property is not readable',
            $object::class,
            $property,
        ));
    }

    public function isReadable(object $object, string $property): bool
    {
        return false;
    }

    public function setValue(object $object, string $property, mixed $value): void
    {
        throw new \LogicException(\sprintf(
            'The %s::$%s property is not writable',
            $object::class,
            $property,
        ));
    }

    public function isWritable(object $object, string $property): bool
    {
        return false;
    }
}
