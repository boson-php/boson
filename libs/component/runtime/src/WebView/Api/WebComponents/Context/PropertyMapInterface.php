<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context;

/**
 * Simplified representation of the element properties list.
 */
interface PropertyMapInterface
{
    /**
     * Returns element's property by name and {@see null} if there is
     * no such property otherwise.
     *
     * @param non-empty-string $property
     */
    public function get(string $property): mixed;

    /**
     * Returns {@see true} if element has a property,
     * and {@see false} otherwise.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/hasOwnProperty
     *
     * @param non-empty-string $property
     */
    public function has(string $property): bool;
}
