<?php

declare(strict_types=1);

namespace Boson\Contracts\Uri\Component;

use Boson\Contracts\Uri\Exception\InvalidArgumentExceptionInterface;

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

    /**
     * Return an instance with the specified username information.
     *
     * This method MUST retain the state of the current instance and return
     * an instance that contains the specified username information.
     *
     * @param non-empty-string|\Stringable $user
     * @throws InvalidArgumentExceptionInterface in case of invalid username argument passed
     */
    public function withUser(\Stringable|string $user): static;

    /**
     * Return an instance with the specified password information.
     *
     * This method MUST retain the state of the current instance and return
     * an instance that contains the specified password information.
     *
     * @param \Stringable|non-empty-string|null $password
     * @throws InvalidArgumentExceptionInterface in case of invalid password argument passed
     */
    public function withPassword(#[\SensitiveParameter] \Stringable|string|null $password): static;
}
