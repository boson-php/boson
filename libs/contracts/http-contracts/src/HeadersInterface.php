<?php

declare(strict_types=1);

namespace Boson\Contracts\Http;

/**
 * @template-extends \Traversable<non-empty-lowercase-string, string>
 */
interface HeadersInterface extends \Traversable, \Countable
{
    /**
     * Returns the first header by name or the default one.
     *
     * @param non-empty-string $name case-insensitive header field name to find
     */
    public function first(string $name, ?string $default = null): ?string;

    /**
     * Returns headers list by name.
     *
     * @param non-empty-string $name case-insensitive header field name to find
     *
     * @return list<string>
     */
    public function all(string $name): array;

    /**
     * Returns {@see true} if the HTTP header is defined.
     *
     * @param non-empty-string $name case-insensitive header field name to find
     */
    public function has(string $name): bool;

    /**
     * Returns {@see true} if the given HTTP header contains
     * the given case sensitive value.
     *
     * @param non-empty-string $name case-insensitive header field name to find
     */
    public function contains(string $name, string $value): bool;

    /**
     * Gets count of the headers.
     *
     * @return int<0, max>
     */
    public function count(): int;
}
