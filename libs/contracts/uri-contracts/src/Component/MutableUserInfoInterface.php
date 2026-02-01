<?php

declare(strict_types=1);

namespace Boson\Contracts\Uri\Component;

use Boson\Contracts\Uri\Exception\InvalidArgumentExceptionInterface;

/**
 * Represents a mutable user information component
 */
interface MutableUserInfoInterface extends UserInfoInterface
{
    public string $user {
        get;
        /**
         * Allows updating the username value in the user info component
         *
         * @param \Stringable|non-empty-string $user
         *
         * @throws InvalidArgumentExceptionInterface if an invalid user info's username is provided
         */
        set(\Stringable|string $user);
    }

    public ?string $password {
        get;
        /**
         * Allows updating the password value in the user info component
         *
         * @param \Stringable|non-empty-string|null $password
         *
         * @throws InvalidArgumentExceptionInterface if an invalid user info's password is provided
         */
        set(#[\SensitiveParameter] \Stringable|string|null $password);
    }
}
