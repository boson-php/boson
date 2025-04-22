<?php

declare(strict_types=1);

namespace Boson\Http\Uri;

use Boson\Http\Uri\Authority\AuthorityValueObjectImpl;

final class Authority implements AuthorityInterface
{
    use AuthorityValueObjectImpl;

    /**
     * Gets the user component of the URI.
     *
     * @uses \Boson\Http\Uri\UserInfoInterface::$user
     *
     * @var non-empty-string|null
     */
    public ?string $user {
        get => $this->userInfo?->user;
    }

    /**
     * Gets the password component of the URI.
     *
     * @uses \Boson\Http\Uri\UserInfoInterface::$password
     *
     * @var non-empty-string|null
     */
    public ?string $password {
        get => $this->userInfo?->password;
    }

    public function __construct(
        /**
         * @var non-empty-string
         */
        public readonly string $host,
        /**
         * @var int<0, 65535>|null
         */
        public readonly ?int $port = null,
        public readonly ?UserInfoInterface $userInfo = null,
    ) {}
}
