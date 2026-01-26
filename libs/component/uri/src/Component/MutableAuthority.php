<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidUriComponentArgumentException;
use Boson\Contracts\Uri\Component\AuthorityInterface;
use Boson\Contracts\Uri\Component\MutableAuthorityInterface;
use Boson\Contracts\Uri\Component\UserInfoInterface;

final class MutableAuthority extends Authority implements
    MutableAuthorityInterface
{
    public ?string $user {
        get => $this->userInfo?->user;
        set(\Stringable|string|null $user) {
            if ($user === null) {
                $this->userInfo = null;
                return;
            }

            if ($this->userInfo === null) {
                $this->userInfo = new UserInfo($user);
            } else {
                $this->userInfo->user = $user;
            }
        }
    }

    /**
     * Gets the password component of the URI.
     *
     * @var non-empty-string|null
     */
    public ?string $password {
        get => $this->userInfo?->password;
    }

    public string $host {
        get => $this->host;
        /**
         * @throws InvalidUriComponentArgumentException in case of invalid host
         *         argument passed
         */
        set(\Stringable|string $host) => $this->formatHostParameter($host);
    }

    public ?int $port = null {
        get => $this->port;
        /**
         * @throws InvalidUriComponentArgumentException in case of invalid port
         *         argument passed
         */
        set(?int $port) => $this->formatPortParameter($port);
    }

    /**
     * Gets a mutable userinfo URI component with a
     * specific {@see MutableUserInfo} implementation.
     *
     * @var MutableUserInfo|null
     */
    public ?UserInfoInterface $userInfo = null {
        get => $this->userInfo;
        /**
         * @throws InvalidUriComponentArgumentException in case of invalid user
         *         info argument passed
         */
        set(?UserInfoInterface $info) => $this->formatUserInfoParameter($info);
    }

    /**
     * Unlike the parent {@see parent::formatUserInfoParameter()} method, it
     * returns a mutable implementation of user info
     */
    #[\Override]
    protected function formatUserInfoParameter(?UserInfoInterface $info): ?MutableUserInfo
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
