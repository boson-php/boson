<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context;

/**
 * @template-extends \Traversable<mixed, non-empty-string>
 */
interface ClassListInterface extends \Traversable, \Countable, \Stringable
{
    /**
     * @link https://developer.mozilla.org/docs/Web/API/DOMTokenList/item
     *
     * @param int<0, max> $index
     *
     * @return non-empty-string|null
     */
    public function findByIndex(int $index): ?string;

    /**
     * Returns {@see true} if element has class name,
     * and {@see false} otherwise.
     *
     * @link https://developer.mozilla.org/docs/Web/API/DOMTokenList/contains
     *
     * @param non-empty-string $class
     */
    public function contains(string $class): bool;

    /**
     * Returns classes count.
     *
     * @return int<0, max>
     */
    public function count(): int;
}
