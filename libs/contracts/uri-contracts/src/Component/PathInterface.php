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
     *
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
     * Return an instance with the specified path segments.
     *
     * This method MUST retain the state of the current instance and return
     * an instance that contains the specified path segments.
     *
     * @param iterable<mixed, \Stringable|non-empty-string> $segments
     *
     * @throws InvalidArgumentExceptionInterface if an invalid path segment is provided
     */
    public function withSegments(iterable $segments): static;

    /**
     * Return an instance with an added segment at the specified position (index)
     *
     * This method MUST retain the state of the current instance and return
     * an instance that contains the specified path segment.
     *
     * When passed a {@see null} index, adds the segment to the end of the list.
     *
     * @param \Stringable|non-empty-string $segment
     * @param int<0, max>|null $index
     *
     * @throws InvalidArgumentExceptionInterface if an invalid path segment or index is provided
     */
    public function withSegment(\Stringable|string $segment, ?int $index = null): static;

    /**
     * Return an instance without a path segment at the specified position (index)
     *
     * This method MUST retain the state of the current instance and return
     * an instance which excludes the path segment at the specified position
     *
     * @param int<0, max> $index
     *
     * @throws InvalidArgumentExceptionInterface if an invalid path index is provided
     */
    public function withoutSegment(int $index): static;

    /**
     * @return int<0, max>
     */
    public function count(): int;
}
