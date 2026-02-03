<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidHostArgumentException;
use Boson\Component\Uri\Exception\InvalidPasswordArgumentException;
use Boson\Component\Uri\Exception\InvalidPortArgumentException;
use Boson\Component\Uri\Exception\InvalidUserArgumentException;
use Boson\Contracts\Uri\Component\AuthorityInterface;
use Boson\Contracts\Uri\Component\MutableAuthorityInterface;
use Boson\Contracts\Uri\Component\UserInfoInterface;

/**
 * @phpstan-consistent-constructor
 */
final class MutableAuthority extends Authority implements
    MutableAuthorityInterface
{
    /**
     * Gets or updates the user of the {@see MutableUserInfo} URI component
     *
     * @var non-empty-string|null
     */
    public ?string $user {
        get => $this->userInfo?->username;
        /**
         * Updates a user of the {@see MutableUserInfo} URI component
         *
         * @throws InvalidUserArgumentException if an invalid user info's username is provided
         * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
         */
        set(\Stringable|string|null $user) {
            if ($user === null) {
                $this->userInfo = null;

                return;
            }

            if ($this->userInfo === null) {
                $this->userInfo = new MutableUserInfo($user);

                return;
            }

            $this->userInfo->username = $user;
        }
    }

    /**
     * Gets or updates the password of the {@see MutableUserInfo} URI component
     *
     * @var non-empty-string|null
     */
    public ?string $password {
        get => $this->userInfo?->password;
        /**
         * Updates a password of the {@see MutableUserInfo} URI component
         *
         * @throws InvalidPasswordArgumentException in case of invalid password value passed
         */
        set(#[\SensitiveParameter] \Stringable|string|null $password) {
            if ($this->userInfo === null) {
                if ($password === null) {
                    return;
                }

                throw InvalidPasswordArgumentException::becauseUserNotDefined();
            }

            $this->userInfo->password = $password;
        }
    }

    public string $host {
        get => $this->host;
        /**
         * @throws InvalidHostArgumentException if an invalid authority host is provided
         */
        set(\Stringable|string $host) => $this->formatHost($host);
    }

    public ?int $port = null {
        get => $this->port;
        /**
         * @throws InvalidPortArgumentException if an invalid authority port is provided
         */
        set(?int $port) => $this->formatPort($port);
    }

    /**
     * Gets a mutable userinfo URI component with a
     * specific {@see MutableUserInfo} implementation.
     *
     * @var MutableUserInfo|null
     *
     * @phpstan-ignore-next-line This is a valid docblock type
     */
    public ?UserInfoInterface $userInfo = null {
        get => $this->userInfo;
        /**
         * @throws InvalidUserArgumentException if an invalid user info's username is provided
         * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
         */
        set(?UserInfoInterface $info) => $this->formatUserInfo($info);
    }

    /**
     * Unlike the parent {@see parent::formatUserInfo()} method, it
     * returns a mutable implementation of user info
     *
     * @throws InvalidUserArgumentException if an invalid user info's username is provided
     * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
     */
    #[\Override]
    protected function formatUserInfo(?UserInfoInterface $info): ?MutableUserInfo
    {
        return MutableUserInfo::tryFrom($info);
    }

    /**
     * Returns mutable authority instance from immutable one
     *
     * @api
     *
     * @throws InvalidHostArgumentException if an invalid authority host is provided
     * @throws InvalidPortArgumentException if an invalid authority port is provided
     * @throws InvalidUserArgumentException if an invalid user info's username is provided
     * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
     */
    public static function fromImmutable(AuthorityInterface $authority): self
    {
        if ($authority instanceof self) {
            return clone $authority;
        }

        return new self(
            host: $authority->host,
            port: $authority->port,
            info: $authority->userInfo,
        );
    }

    /**
     * Returns optional mutable authority instance from immutable one
     *
     * @api
     *
     * @throws InvalidHostArgumentException if an invalid authority host is provided
     * @throws InvalidPortArgumentException if an invalid authority port is provided
     * @throws InvalidUserArgumentException if an invalid user info's username is provided
     * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
     */
    public static function tryFromImmutable(?AuthorityInterface $authority): ?self
    {
        if ($authority === null) {
            return null;
        }

        return self::fromImmutable($authority);
    }
}
