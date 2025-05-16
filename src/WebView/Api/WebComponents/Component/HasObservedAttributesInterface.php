<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Component;

interface HasObservedAttributesInterface
{
    /**
     * Called when attributes are changed, added, removed, or replaced.
     *
     * @param non-empty-string $attribute
     */
    public function onAttributeChanged(string $attribute, ?string $value, ?string $previous): void;

    /**
     * Should return an array containing the names of all attributes for which
     * the element needs change notifications.
     *
     * @return list<non-empty-string>
     */
    public static function getObservedAttributeNames(): array;
}
