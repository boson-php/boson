<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context;

/**
 * Simplified representation of the element attributes list.
 *
 * @template-extends \Traversable<non-empty-string, string>
 */
interface AttributeMapInterface extends \Traversable, \Countable
{
    /**
     * Returns element's first attribute whose qualified name is qualifiedName,
     * and {@see null} if there is no such attribute otherwise.
     *
     * @link https://developer.mozilla.org/docs/Web/API/Element/getAttribute
     *
     * @param non-empty-string $attribute
     */
    public function get(string $attribute): ?string;

    /**
     * Returns {@see true} if element has an attribute whose qualified name
     * is qualifiedName, and {@see false} otherwise.
     *
     * @link https://developer.mozilla.org/docs/Web/API/Element/hasAttribute
     *
     * @param non-empty-string $attribute
     */
    public function has(string $attribute): bool;

    /**
     * Returns attributes count.
     *
     * @return int<0, max>
     */
    public function count(): int;
}
