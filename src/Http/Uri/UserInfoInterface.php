<?php

declare(strict_types=1);

namespace Boson\Http\Uri;

use Boson\Shared\ValueObject\StringValueObjectInterface;

/**
 * Represents the user information component of an {@see AuthorityInterface}.
 *
 * @link https://datatracker.ietf.org/doc/html/rfc3986#section-3.2.1
 */
interface UserInfoInterface extends StringValueObjectInterface
{
    /**
     * @var non-empty-string
     */
    public const string USER_PASSWORD_DELIMITER = ':';

    /**
     * Gets user name of the user information component.
     *
     * ```
     * abc://user:pass@example.com:123/path/data?k=val&k2=val2#frag
     *       |--|
     *       |
     *    username
     * ```
     *
     * The username cannot be omitted. If the user info is missing, the
     * {@see UserInfoInterface} itself should not be defined ({@see null})
     * in the {@see AuthorityInterface::$userInfo} property.
     *
     * @var non-empty-string
     */
    public string $user { get; }

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
     * @var non-empty-string|null
     */
    public ?string $password { get; }
}
