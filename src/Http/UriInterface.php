<?php

declare(strict_types=1);

namespace Boson\Http;

use Boson\Http\Uri\AuthorityInterface;
use Boson\Http\Uri\PathInterface;
use Boson\Http\Uri\QueryInterface;
use Boson\Http\Uri\SchemeInterface;
use Boson\Shared\ValueObject\StringValueObjectInterface;

/**
 * Value object representing a URI.
 *
 * The URI is structured as follows:
 *
 * ```
 * abc://user:pass@example.com:123/path/data?k=val&k2=val2#frag
 * |-|   |-----------------------| |-------| |-----------| |--|
 * |                 |                 |           |          |
 * scheme        authority           path        query fragment
 * ```
 */
interface UriInterface extends StringValueObjectInterface
{
    /**
     * Gets the scheme of this {@see UriInterface}.
     *
     * The URI scheme refers to a specification for assigning identifiers
     * within that scheme. Only absolute URIs contain a scheme component, but
     * not all absolute URIs will contain a scheme component. Although scheme
     * names are case-insensitive, the canonical form is lowercase.
     *
     * ```
     * abc://user:pass@example.com:123/path/data?k=val&k2=val2#frag
     * |-|
     * |
     * scheme
     * ```
     *
     * The {@see SchemeInterface} component for absolute URI.
     *
     * ```
     * echo $uri; // "http://example.com"
     *
     * assert($uri->scheme, Scheme::Http);
     * ```
     *
     * The {@see SchemeInterface} component for relative URI.
     *
     * ```
     * echo $uri; // "/hello/world"
     *
     * assert($uri->scheme, null);
     * ```
     *
     * @link https://datatracker.ietf.org/doc/html/rfc3986#section-3.1
     */
    public ?SchemeInterface $scheme { get; }

    /**
     * Gets the authority of this {@see UriInterface}.
     *
     * The authority is a hierarchical element for naming authority such that
     * the remainder of the URI is delegated to that authority. For HTTP, the
     * authority consists of the host and port. The host portion of the
     * authority is case-insensitive.
     *
     * The authority also includes a `username:password` component, however
     * the use of this is deprecated and should be avoided.
     *
     * ```
     * abc://user:pass@example.com:123/path/data?k=val&k2=val2#frag
     *       |-----------------------|
     *                   |
     *               authority
     * ```
     *
     * The {@see AuthorityInterface} component for absolute URI.
     *
     * ```
     * echo $uri; // "http://example.org:80/hello/world"
     *
     * assert((string) $uri->authority, 'example.org:80');
     * ```
     *
     * The {@see AuthorityInterface} component for relative URI.
     *
     * ```
     * echo $uri; // "/hello/world"
     *
     * assert($uri->authority, null);
     * ```
     *
     * @link https://tools.ietf.org/html/rfc3986#section-3.2
     */
    public ?AuthorityInterface $authority { get; }

    /**
     * Gets the path of this {@see UriInterface}.
     *
     * ```
     * abc://user:pass@example.com:123/path/data?k=val&k2=val2#frag
     *                                 |-------|
     *                                     |
     *                                   path
     * ```
     *
     * If the URI is `*` then the path component is equal to `*`.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-3.2
     */
    public PathInterface $path { get; }

    /**
     * Gets the query string component of this {@see UriInterface}.
     *
     * The query component contains non-hierarchical data that, along with data
     * in the path component, serves to identify a resource within the scope of
     * the URI's scheme and naming authority (if any). The query component is
     * indicated by the first question mark ("?") character and terminated by a
     * number sign ("#") character or by the end of the URI.
     *
     * ```
     * abc://user:pass@example.com:123/path/data?k=val&k2=val2#frag
     *                                           |-----------|
     *                                                 |
     *                                               query
     * ```
     *
     * For example:
     *
     * ```
     * echo $uri; // "/hello/world?key=value&foo=bar"
     *
     * assert(count($uri->query), 2);
     * ```
     *
     * Without a query string component:
     *
     * ```
     * echo $uri; // "/hello/world"
     *
     * assert(count($uri->query), 0);
     * ```
     *
     * @link https://datatracker.ietf.org/doc/html/rfc3986#section-3.4
     */
    public QueryInterface $query { get; }

    /**
     * Gets the fragment string component of this {@see UriInterface}.
     *
     * ```
     * abc://user:pass@example.com:123/path/data?k=val&k2=val2#frag
     *                                                         |--|
     *                                                            |
     *                                                     fragment
     * ```
     *
     * @link https://datatracker.ietf.org/doc/html/rfc3986#section-3.5
     *
     * @var non-empty-string|null
     */
    public ?string $fragment { get; }
}
