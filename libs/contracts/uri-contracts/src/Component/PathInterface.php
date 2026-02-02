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
 * @template-extends \Traversable<int<0, max>, non-empty-string>
 * @template-extends \ArrayAccess<int<0, max>, non-empty-string>
 */
interface PathInterface extends
    UriComponentInterface,
    \Traversable,
    \ArrayAccess,
    \Countable
{
    /**
     * Indicates whether the path is absolute (starts with a leading slash).
     *
     * An absolute path starts with `/` (e.g., "/users/profile"), while a
     * relative path does not (e.g., "users/profile").
     */
    public bool $isAbsolute {
        get;
    }

    /**
     * Indicates whether the path has a trailing slash.
     *
     * When {@see true}, the path string representation will end with `/`
     * (e.g., "/users/"). This is often used to distinguish directory paths
     * from file paths.
     */
    public bool $hasTrailingSlash {
        get;
    }

    /**
     * Returns {@see true} if the path is empty (has no path segments).
     */
    public bool $isEmpty {
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
     * NOTE: The resulting string MUST be escaped into URI format according
     * to the specification (specification standard may be implementation
     * dependent, for example WhatWG, RFC3986, etc.)
     *
     * @link https://url.spec.whatwg.org/
     * @link https://datatracker.ietf.org/doc/html/rfc3986
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
     *
     * NOTE: The resulting string MUST be escaped into URI format according
     * to the specification (specification standard may be implementation
     * dependent, for example {@link https://url.spec.whatwg.org/ WhatWG},
     * {@link https://datatracker.ietf.org/doc/html/rfc3986 RFC-3986}, etc.)
     */
    public string $relative {
        get;
    }

    /**
     * Returns the path segment at the specified position (index). If there
     * is no data at the passed index, it returns {@see null}.
     *
     * The path (segment) value is returned "as is", without any encoding.
     *
     * @param int<0, max> $index
     *
     * @return non-empty-string|null
     * @throws InvalidArgumentExceptionInterface if an invalid path index is provided
     */
    public function at(int $index): ?string;

    /**
     * Returns {@see true} if the path contains the given segment.
     * Otherwise, returns {@see false}.
     *
     * The URI path component is generally CASE-SENSITIVE according to the
     * RFC-3986 spec ({@link https://datatracker.ietf.org/doc/html/rfc3986}).
     *
     * The path (segment) value is returned "as is", without any encoding.
     *
     * @param \Stringable|non-empty-string $segment
     *
     * @throws InvalidArgumentExceptionInterface if an invalid path segment is provided
     */
    public function contains(\Stringable|string $segment): bool;

    /**
     * @return int<0, max>
     */
    public function count(): int;
}
