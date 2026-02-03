<?php

declare(strict_types=1);

namespace Boson\Contracts\Uri\Component;

use Boson\Contracts\Uri\UriInterface;

/**
 * Represents the authority component of an {@see UriInterface}.
 *
 * @link https://tools.ietf.org/html/rfc3986#section-3.2
 */
interface AuthorityInterface extends UriComponentInterface
{
    /**
     * The userinfo subcomponent may consist of a username and, optionally,
     * scheme-specific information about how to gain authorization to access
     * the resource. The user information, if present, is followed by a
     * commercial at-sign (`@`) that delimits it from the host.
     *
     * ```
     * abc://user:pass@example.com:123/path/data?k=val&k2=val2#frag
     *       |-------|
     *           |
     *       user info
     * ```
     *
     * @link https://datatracker.ietf.org/doc/html/rfc3986#section-3.2.1
     */
    public ?UserInfoInterface $userInfo { get; }

    /**
     * The host subcomponent of authority is identified by an IP literal
     * encapsulated within square brackets, an IPv4 address in
     * dotted-decimal form, or a registered name.
     *
     * ```
     * abc://user:pass@example.com:123/path/data?k=val&k2=val2#frag
     *                 |---------|
     *                      |
     *                    host
     * ```
     *
     * In case of host is not defined, then the {@see UriInterface::$authority}
     * should e omitted (defined as {@see null}).
     *
     * @link https://datatracker.ietf.org/doc/html/rfc3986#section-3.2.2
     *
     * @var non-empty-string
     */
    public string $host { get; }

    /**
     * The port subcomponent of authority is designated by an optional port
     * number in decimal following the host and delimited from it by a
     * single colon (':') character.
     *
     * Each scheme also may define a default port: For example, the `HTTP`
     * scheme defines a default port of `80`, corresponding to its reserved
     * `TCP` port number. The type of port designated by the port number
     * (e.g., `TCP`, `UDP`, `SCTP`) is defined by the URI scheme.
     *
     * ```
     * abc://user:pass@example.com:123/path/data?k=val&k2=val2#frag
     *                             |-|
     *                              |
     *                            port
     * ```
     *
     *
     * Note: According to the RFC-3986, section 3.2.3, a port can be an
     *       arbitrary number with an infinite digit, but from an
     *       implementation perspective, the port size will likely not exceed
     *       the maximum bit depth of a PHP {@see int} type ({@see PHP_INT_MAX}).
     *       All built-in parsers also rely on this (int32/int64 max) value.
     *
     * @link https://datatracker.ietf.org/doc/html/rfc3986#section-3.2.3
     *
     * @var int<0, max>|null
     */
    public ?int $port { get; }
}
