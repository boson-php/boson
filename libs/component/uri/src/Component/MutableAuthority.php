<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidHostArgumentException;
use Boson\Component\Uri\Exception\InvalidPasswordArgumentException;
use Boson\Component\Uri\Exception\InvalidPortArgumentException;
use Boson\Component\Uri\Exception\InvalidUriComponentArgumentException;
use Boson\Component\Uri\Exception\InvalidUserArgumentException;
use Boson\Contracts\Uri\Component\AuthorityInterface;
use Boson\Contracts\Uri\Component\MutableAuthorityInterface;
use Boson\Contracts\Uri\Component\UserInfoInterface;

final class MutableAuthority extends Authority implements
    MutableAuthorityInterface
{
    /**
     * Gets or updates the user of the {@see MutableUserInfo} URI component
     *
     * @var non-empty-string|null
     */
    public ?string $user {
        get => $this->userInfo?->user;
        /**
         * Updates a user of the {@see MutableUserInfo} URI component
         *
         * @throws InvalidUserArgumentException in case of invalid username value passed
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

            $this->userInfo->user = $user;
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
         * @throws InvalidHostArgumentException in case of invalid host argument passed
         */
        set(\Stringable|string $host) => $this->formatHostArgument($host);
    }

    public ?int $port = null {
        get => $this->port;
        /**
         * @throws InvalidPortArgumentException in case of invalid port argument passed
         */
        set(?int $port) => $this->formatPortArgument($port);
    }

    /**
     * Gets a mutable userinfo URI component with a
     * specific {@see MutableUserInfo} implementation.
     *
     * @var MutableUserInfo|null
     * @phpstan-ignore-next-line This is a valid docblock type
     */
    public ?UserInfoInterface $userInfo = null {
        get => $this->userInfo;
        /**
         * @throws InvalidUriComponentArgumentException in case of invalid user
         *         info argument passed
         */
        set(?UserInfoInterface $info) => $this->formatUserInfoArgument($info);
    }

    /**
     * Unlike the parent {@see parent::formatUserInfoArgument()} method, it
     * returns a mutable implementation of user info
     */
    #[\Override]
    protected function formatUserInfoArgument(?UserInfoInterface $info): ?MutableUserInfo
    {
        return MutableUserInfo::tryFromImmutable($info);
    }

    /**
     * Returns mutable authority instance from immutable one
     *
     * @api
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
     */
    public static function tryFromImmutable(?AuthorityInterface $authority): ?self
    {
        if ($authority === null) {
            return null;
        }

        return self::fromImmutable($authority);
    }
}
