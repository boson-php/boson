<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Component;

interface HasPropertiesInterface
{
    /**
     * Called when property are changed.
     *
     * @param non-empty-string $property
     */
    public function onPropertyChanged(string $property, mixed $value): void;

    /**
     * Should return an array containing the names of all properties for which
     * the element needs change notifications.
     *
     * ```
     * return ['propertyName', 'otherPropertyName'];
     * ```
     *
     * @return list<non-empty-string>
     */
    public static function getPropertyNames(): array;
}
