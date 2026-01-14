<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Contracts\Uri\Component\AuthorityInterface;
use Boson\Contracts\Uri\Component\UserInfoInterface;

final class Authority implements AuthorityInterface
{
    /**
     * Gets the user component of the URI.
     *
     * @var non-empty-string|null
     */
    public ?string $user {
        get => $this->userInfo?->user;
    }

    /**
     * Gets the password component of the URI.
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

    public function equals(mixed $other): bool
    {
        return $other === $this
            || ($other instanceof self
                && $this->host === $other->host
                && $this->port === $other->port
                && ($other->userInfo === $this->userInfo
                    || $other->userInfo?->equals($this->userInfo) === true));
    }

    public function toString(): string
    {
        return (string) $this;
    }

    public function __toString(): string
    {
        $result = $this->host;

        if ($this->port !== null) {
            $result .= ':' . $this->port;
        }

        if ($this->userInfo !== null) {
            return $this->userInfo . '@' . $result;
        }

        return $result;
    }
}
