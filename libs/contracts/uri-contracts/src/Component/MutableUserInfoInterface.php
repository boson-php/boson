<?php

declare(strict_types=1);

namespace Boson\Contracts\Uri\Component;

use Boson\Contracts\Uri\Exception\InvalidArgumentExceptionInterface;

/**
 * A mutable user information component interface.
 */
interface MutableUserInfoInterface extends UserInfoInterface
{
    /**
     * {@inheritDoc}
     *
     * @var non-empty-string
     */
    public string $user {
        get;
        /**
         * Allows updating the username value in the user info component
         *
         * @param \Stringable|non-empty-string $user
         * @throws InvalidArgumentExceptionInterface in case of invalid
         *         username argument value has been passed
         */
        set(\Stringable|string $user);
    }

    /**
     * {@inheritDoc}
     *
     * @var non-empty-string|null
     */
    public ?string $password {
        get;
        /**
         * Allows updating the password value in the user info component
         *
         * @param \Stringable|non-empty-string|null $password
         * @throws InvalidArgumentExceptionInterface in case of invalid
         *         password argument value has been passed
         */
        set(#[\SensitiveParameter] \Stringable|string|null $password);
    }
}
