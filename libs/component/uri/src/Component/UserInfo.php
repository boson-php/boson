<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Contracts\Uri\Component\UserInfoInterface;

final readonly class UserInfo implements UserInfoInterface
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public string $user,
        /**
         * @var non-empty-string|null
         */
        #[\SensitiveParameter]
        public ?string $password = null,
    ) {}

    public function equals(mixed $other): bool
    {
        return $other === $this
            || (
                $other instanceof self
                && $other->user === $this->user
                && $other->password === $this->password
            );
    }

    public function toString(): string
    {
        return (string) $this;
    }

    public function __toString(): string
    {
        if ($this->password !== null) {
            return $this->user . ':' . $this->password;
        }

        return $this->user;
    }
}
