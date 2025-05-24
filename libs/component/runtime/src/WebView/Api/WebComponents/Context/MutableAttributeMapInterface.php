<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context;

/**
 * Simplified mutable representation of the element attributes list.
 */
interface MutableAttributeMapInterface extends AttributeMapInterface
{
    /**
     * Sets the value of element's first attribute whose qualified
     * name is qualifiedName to value.
     *
     * @link https://developer.mozilla.org/docs/Web/API/Element/setAttribute
     *
     * @param non-empty-string $attribute
     */
    public function set(string $attribute, string $value): void;

    /**
     * Removes element's first attribute whose qualified name is qualifiedName.
     *
     * @link https://developer.mozilla.org/docs/Web/API/Element/removeAttribute
     *
     * @param non-empty-string $attribute
     */
    public function remove(string $attribute): void;
}
