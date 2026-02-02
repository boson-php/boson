<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidPasswordArgumentException;
use Boson\Component\Uri\Exception\InvalidUserArgumentException;
use Boson\Contracts\Uri\Component\MutableUserInfoInterface;

/**
 * @phpstan-consistent-constructor
 */
final class MutableUserInfo extends UserInfo implements MutableUserInfoInterface
{
    public string $username {
        get => $this->user;
        /**
         * @throws InvalidUserArgumentException if an invalid user info's username is provided
         */
        set(\Stringable|string $user) => $this->formatUser($user);
    }

    public ?string $password = null {
        get => $this->password;
        /**
         * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
         */
        set(#[\SensitiveParameter] \Stringable|string|null $password) => $this->formatPassword($password);
    }
}
