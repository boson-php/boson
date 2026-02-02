<?php

declare(strict_types=1);

namespace Boson\Contracts\Uri\Component;

/**
 * Represents the user information component of an {@see AuthorityInterface}.
 *
 * @link https://datatracker.ietf.org/doc/html/rfc3986#section-3.2.1
 */
interface UserInfoInterface extends UriComponentInterface
{
    /**
     * Gets the username of the user information component.
     *
     * ```
     * abc://user:pass@example.com:123/path/data?k=val&k2=val2#frag
     *       |--|
     *       |
     *    username
     * ```
     *
     * Please note that an empty username may indicate its absence, but
     * the presence of {@see UserInfoInterface}, for example:
     * ```
     * abc://:pass@example.com
     *       |
     *  empty username
     * ```
     */
    public string $username { get; }

    /**
     * Gets optional user password of the user information component.
     *
     * ```
     * abc://user:pass@example.com:123/path/data?k=val&k2=val2#frag
     *            |--|
     *            |
     *         password
     * ```
     *
     * Note: NO password ({@see null}) and a BLANK password are two
     *       different values:
     * ```
     * abc://user:@example.com
     *           |
     *      empty password
     *
     * abc://user@example.com
     *           |
     *      no password
     * ```
     */
    public ?string $password { get; }
}
