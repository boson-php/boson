<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context;

/**
 * Simplified mutable representation of the element properties list.
 */
interface MutablePropertyMapInterface extends PropertyMapInterface
{
    /**
     * Sets the value of element's property by its name.
     *
     * @param non-empty-string $property
     */
    public function set(string $property, mixed $value): void;

    /**
     * Removes element's property by its name.
     *
     * @param non-empty-string $property
     */
    public function remove(string $property): void;
}
