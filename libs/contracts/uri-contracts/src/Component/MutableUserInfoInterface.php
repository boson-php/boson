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
         * @throws InvalidArgumentExceptionInterface in case of invalid
         *         username argument value has been passed
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
         * @throws InvalidArgumentExceptionInterface in case of invalid
         *         password argument value has been passed
         */
        set(#[\SensitiveParameter] \Stringable|string|null $password);
    }
}
