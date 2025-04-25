<?php

declare(strict_types=1);

namespace Boson\Http;

/**
 * @template-extends \Traversable<non-empty-lowercase-string, \Stringable|string>
 * @template-extends \ArrayAccess<non-empty-lowercase-string, list<\Stringable|string>>
 */
interface HeadersInterface extends \Traversable, \ArrayAccess, \Countable
{
    /**
     * @param non-empty-string $name
     * @return ($default is null ? \Stringable|string|null : \Stringable|string)
     */
    public function first(string $name, \Stringable|string|null $default = null): \Stringable|string|null;

    /**
     * @param non-empty-string $name
     * @return iterable<array-key, \Stringable|string>
     */
    public function all(string $name): iterable;

    /**
     * @param non-empty-string $name
     */
    public function contains(string $name): bool;
}
