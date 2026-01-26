<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidUriComponentArgumentException;
use Boson\Contracts\Uri\Component\MutableUserInfoInterface;

final class MutableUserInfo extends UserInfo implements MutableUserInfoInterface
{
    public string $user {
        get => parent::$user::get();
        /**
         * @throws InvalidUriComponentArgumentException in case of invalid username value passed
         */
        set(\Stringable|string $user) {
            $this->user = $this->formatUserParameter($user);
        }
    }

    public ?string $password {
        get => parent::$password::get();
        /**
         * @throws InvalidUriComponentArgumentException in case of invalid password value passed
         */
        set(#[\SensitiveParameter] \Stringable|string|null $password) {
            $this->password = $this->formatPasswordParameter($password);
        }
    }
}
