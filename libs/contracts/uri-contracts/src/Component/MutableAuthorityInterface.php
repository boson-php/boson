<?php

declare(strict_types=1);

namespace Boson\Contracts\Uri\Component;

use Boson\Contracts\Uri\Exception\InvalidArgumentExceptionInterface;

/**
 * Represents mutable authority component
 */
interface MutableAuthorityInterface extends AuthorityInterface
{
    /**
     * Gets a mutable user info component
     *
     * The type hint {@see UserInfoInterface} is caused by the impossibility
     * of extending getter properties when inheriting classes
     *
     * @var MutableUserInfoInterface|null
     */
    public ?UserInfoInterface $userInfo {
        get;
        /**
         * Allows updating authority user information
         *
         * @param UserInfoInterface|null $userInfo
         *
         * @return void
         */
        set(UserInfoInterface|null $userInfo);
    }

    public string $host {
        get;
        /**
         * Allows updating authority host value
         *
         * @param \Stringable|non-empty-string $host
         *
         * @throws InvalidArgumentExceptionInterface in case of invalid
         *         host argument value has been passed
         */
        set(\Stringable|string $host);
    }

    public ?int $port {
        get;
        /**
         * Allows updating authority port value
         *
         * @param int<0, 65535>|null $port
         *
         * @throws InvalidArgumentExceptionInterface in case of invalid
         *         port argument value has been passed
         */
        set(?int $port);
    }
}
