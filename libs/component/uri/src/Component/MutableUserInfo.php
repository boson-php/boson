<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidUriComponentArgumentException;
use Boson\Contracts\Uri\Component\MutableUserInfoInterface;
use Boson\Contracts\Uri\Component\UserInfoInterface;

final class MutableUserInfo extends UserInfo implements MutableUserInfoInterface
{
    public string $user {
        get => $this->user;
        /**
         * @throws InvalidUriComponentArgumentException in case of invalid username value passed
         */
        set(\Stringable|string $user) => $this->formatUserParameter($user);
    }

    public ?string $password = null {
        get => $this->password;
        /**
         * @throws InvalidUriComponentArgumentException in case of invalid password value passed
         */
        set(#[\SensitiveParameter] \Stringable|string|null $password) => $this->formatPasswordParameter($password);
    }

    /**
     * Returns mutable user info instance from immutable one
     *
     * @api
     */
    final public static function fromImmutable(UserInfoInterface $info): self
    {
        if ($info instanceof self) {
            return clone $info;
        }

        return new self(
            user: $info->user,
            password: $info->password,
        );
    }

    /**
     * Returns optional mutable user info instance from immutable one
     *
     * @api
     */
    final public static function tryFromImmutable(?UserInfoInterface $info): ?self
    {
        if ($info === null) {
            return null;
        }

        return self::fromImmutable($info);
    }
}
