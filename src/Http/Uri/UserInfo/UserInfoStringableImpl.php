<?php

declare(strict_types=1);

namespace Boson\Http\Uri\UserInfo;

use Boson\Http\Uri\UserInfoInterface;

/**
 * @phpstan-require-implements UserInfoInterface
 */
trait UserInfoStringableImpl
{
    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract public string $user { get; }
    // abstract public ?string $password { get; }
    //

    public function __toString(): string
    {
        if ($this->password !== null) {
            return $this->user
                . UserInfoInterface::USER_PASSWORD_DELIMITER
                . $this->password;
        }

        return $this->user;
    }
}
