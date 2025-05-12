<?php

declare(strict_types=1);

namespace Boson\Shared\PropertyAccessor;

interface PropertyAccessorInterface
{
    /**
     * Returns the value of the specified field from the passed object.
     *
     * @param non-empty-string $property
     *
     * @throws \Throwable occurs in case of value cannot be reads
     */
    public function getValue(object $object, string $property): mixed;

    /**
     * Returns {@see true} if the field is readable by the implementation,
     * and {@see false} otherwise.
     *
     * This method MUST NOT throw any exceptions.
     *
     * @param non-empty-string $property
     */
    public function isReadable(object $object, string $property): bool;

    /**
     * Updates the value of the specified field inside the passed object.
     *
     * @param non-empty-string $property
     *
     * @throws \Throwable occurs in case of value cannot be writes
     */
    public function setValue(object $object, string $property, mixed $value): void;

    /**
     * Returns {@see true} if the field is writable by the implementation,
     * and {@see false} otherwise.
     *
     * This method MUST NOT throw any exceptions.
     *
     * @param non-empty-string $property
     */
    public function isWritable(object $object, string $property): bool;
}
