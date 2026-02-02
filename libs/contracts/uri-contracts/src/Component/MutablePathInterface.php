<?php

declare(strict_types=1);

namespace Boson\Contracts\Uri\Component;

use Boson\Contracts\Uri\Exception\InvalidArgumentExceptionInterface;

interface MutablePathInterface extends PathInterface
{
    public bool $isAbsolute {
        get;
        /**
         * Allows to modify the {@see $isAbsolute} value.
         */
        set;
    }

    public bool $hasTrailingSlash {
        get;
        /**
         * Allows to modify the {@see $hasTrailingSlash} value.
         */
        set;
    }

    /**
     * Replaces all path segments with the provided iterable collection.
     *
     * This method clears all existing segments and replaces them with the
     * provided segments.
     *
     * ```
     * $path->putAll(['users', '123', 'profile']);
     * // Resulting path: "users/123/profile"
     * ```
     *
     * @param iterable<mixed, \Stringable|non-empty-string> $segments
     *
     * @throws InvalidArgumentExceptionInterface if an invalid path segment is provided
     */
    public function putAll(iterable $segments): void;

    /**
     * Inserts or replaces a single path segment at the specified index.
     *
     * In case of:
     * - `$index` is {@see null} or greater than segments count, the segment
     *   is appended to the end of the path.
     * - `$index` is specified and within bounds, the segment replaces the
     *    existing segment at that position.
     *
     * ```
     * $path->put('new', 1); // Insert at position 1
     * $path->put('append'); // Append to the end (null index)
     * ```
     *
     * @param \Stringable|non-empty-string $segment
     * @param int<0, max>|null $index
     *
     * @throws InvalidArgumentExceptionInterface if an invalid path segment or index is provided
     */
    public function put(\Stringable|string $segment, ?int $index = null): void;

    /**
     * Removes a path segment at the specified index.
     *
     * After removal, subsequent segments shift left to fill the gap.
     * The path structure (absolute/trailing slash) remains unchanged.
     *
     * ```
     * // Path: "users/123/profile"
     * $path->remove(1);
     * // Result: "users/profile"
     * ```
     *
     * @param int<0, max> $index
     *
     * @throws InvalidArgumentExceptionInterface if an invalid path index is provided
     */
    public function remove(int $index): void;
}
