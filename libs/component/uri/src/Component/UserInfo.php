<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidPasswordArgumentException;
use Boson\Component\Uri\Exception\InvalidUserArgumentException;
use Boson\Contracts\Uri\Component\UserInfoInterface;

/**
 * @phpstan-sealed MutableUserInfo
 *
 * @phpstan-consistent-constructor
 */
class UserInfo implements UserInfoInterface
{
    public protected(set) string $username;

    public protected(set) ?string $password;

    /**
     * @param \Stringable|string $user
     * @param \Stringable|string|null $password
     *
     * @throws InvalidUserArgumentException if an invalid user info's username is provided
     * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
     */
    public function __construct(
        \Stringable|string $user,
        #[\SensitiveParameter]
        \Stringable|string|null $password = null,
    ) {
        $this->user = $this->formatUser($user);
        $this->password = $this->formatPassword($password);
    }

    /**
     * Returns a user info instance from another one
     *
     * @api
     *
     * @throws InvalidUserArgumentException if an invalid user info's username is provided
     * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
     */
    final public static function from(UserInfoInterface $info): static
    {
        if ($info instanceof static) {
            return clone $info;
        }

        return new static(
            user: $info->user,
            password: $info->password,
        );
    }

    /**
     * Returns a user info instance from another one
     *
     * @api
     *
     * @throws InvalidUserArgumentException if an invalid user info's username is provided
     * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
     */
    final public static function tryFrom(?UserInfoInterface $info): ?static
    {
        if ($info === null) {
            return null;
        }

        return static::from($info);
    }

    /**
     * @throws InvalidUserArgumentException if an invalid user info's username is provided
     */
    protected function formatUser(\Stringable|string $user): string
    {
        if ($user instanceof \Stringable) {
            try {
                $user = (string) $user;
                /** @phpstan-ignore-next-line : This is not a dead catch */
            } catch (\Throwable $e) {
                throw InvalidUserArgumentException::becauseStringableErrorOccurs($e);
            }
        }

        return $user;
    }

    /**
     * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
     */
    protected function formatPassword(#[\SensitiveParameter] \Stringable|string|null $password): ?string
    {
        if ($password instanceof \Stringable) {
            try {
                $password = (string) $password;
                /** @phpstan-ignore-next-line : This is not a dead catch */
            } catch (\Throwable $e) {
                throw InvalidPasswordArgumentException::becauseStringableErrorOccurs($e);
            }
        }

        return $password;
    }

    final public function equals(mixed $other): bool
    {
        return $other === $this
            || ($other instanceof UserInfoInterface
                && $other->user === $this->user
                && $other->password === $this->password);
    }

    final public function toString(): string
    {
        return (string) $this;
    }

    final public function __toString(): string
    {
        if ($this->password !== null) {
            return $this->user . ':' . $this->password;
        }

        return $this->user;
    }
}
