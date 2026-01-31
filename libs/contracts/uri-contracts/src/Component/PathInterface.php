<?php

declare(strict_types=1);

namespace Boson\Contracts\Uri\Component;

use Boson\Contracts\Uri\Exception\InvalidArgumentExceptionInterface;
use Boson\Contracts\Uri\UriInterface;

/**
 * Represents the path component of an {@see UriInterface}.
 *
 * @link https://datatracker.ietf.org/doc/html/rfc3986#section-3.3
 *
 * @template-extends \Traversable<array-key, non-empty-string>
 */
interface PathInterface extends
    UriComponentInterface,
    \Traversable,
    \Countable
{
    /**
     * Returns {@see true} if the path is absolute.
     *
     * ```
     * /path/to/file // true
     * path/to/file  // false
     * ```
     */
    public bool $isAbsolute {
        get;
    }

    /**
     * Returns {@see true} if the path has trailing slash.
     *
     *  ```
     *  path/to/file/ // true
     *  path/to/file  // false
     *  ```
     */
    public bool $hasTrailingSlash {
        get;
    }

    /**
     * Gets the absolute path as a string.
     *
     * Please note that when retrieving an explicit relative path, the
     * trailing slash ({@see $hasTrailingSlash}) parameter is ignored. This
     * means that the value is returned without taking into account the
     * trailing slash, even if it is present.
     *
     * This behavior is intended for convenience when compared with the
     * desired value and to avoid possible comparison errors.
     *
     * ```
     * // uri = http://example.com
     *
     * if ($uri->path->absolute === '/') {
     *     ...
     * }
     * ```
     *
     * @var non-empty-string
     */
    public string $absolute {
        get;
    }

    /**
     * Gets the relative path as a string.
     *
     * Please note that when retrieving an explicit relative path, the
     * trailing slash ({@see $hasTrailingSlash}) parameter is ignored. This
     * means that the value is returned without taking into account the
     * trailing slash, even if it is present.
     *
     * This behavior is intended for convenience when compared with the
     * desired value and to avoid possible comparison errors.
     *
     * ```
     * // uri = http://example.com/home
     *
     * if ($uri->path->relative === 'home') {
     *     ...
     * }
     * ```
     */
    public string $relative {
        get;
    }

    /**
     * Returns the path segment at the specified position (index). If there
     * is no data at the passed index, it returns {@see null}.
     *
     * @param int<0, max> $index
     *
     * @return non-empty-string|null
     * @throws InvalidArgumentExceptionInterface in case of invalid index passed
     */
    public function at(int $index): ?string;

    /**
     * Returns {@see true} if the path contains the given segment.
     * Otherwise, returns {@see false}.
     *
     * @param \Stringable|non-empty-string $segment
     *
     * @throws InvalidArgumentExceptionInterface in case of invalid segment passed
     */
    public function contains(\Stringable|string $segment): bool;

    /**
     * @return int<0, max>
     */
    public function count(): int;
}
