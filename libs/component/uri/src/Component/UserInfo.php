<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidUriComponentArgumentException;
use Boson\Contracts\Uri\Component\MutableUserInfoInterface;
use Boson\Contracts\Uri\Component\UserInfoInterface;

/**
 * @phpstan-sealed MutableUserInfo
 */
class UserInfo implements UserInfoInterface
{
    public protected(set) string $user;

    public protected(set) ?string $password;

    /**
     * @param \Stringable|non-empty-string $user
     * @param \Stringable|non-empty-string|null $password
     *
     * @throws InvalidUriComponentArgumentException in case of invalid user
     *         info argument passed
     */
    public function __construct(
        \Stringable|string $user,
        #[\SensitiveParameter]
        \Stringable|string|null $password = null,
    ) {
        $this->user = $this->formatUserParameter($user);
        $this->password = $this->formatPasswordParameter($password);
    }

    /**
     * Returns an immutable user info instance from another one
     *
     * @api
     */
    final public static function from(UserInfoInterface $info): self
    {
        if ($info instanceof self && !$info instanceof MutableUserInfoInterface) {
            return clone $info;
        }

        return new self(
            user: $info->user,
            password: $info->password,
        );
    }

    /**
     * Returns an immutable user info instance from another one
     *
     * @api
     */
    final public static function tryFrom(?UserInfoInterface $info): ?self
    {
        if ($info === null) {
            return null;
        }

        return self::from($info);
    }

    /**
     * Return an instance with the specified username information.
     *
     * This method MUST retain the state of the current instance and return
     * an instance that contains the specified username information.
     *
     * @api
     * @param non-empty-string|\Stringable $user
     * @throws InvalidUriComponentArgumentException in case of invalid username argument passed
     */
    final public function withUser(\Stringable|string $user): static
    {
        $self = clone $this;
        $self->user = $this->formatUserParameter($user);

        return $self;
    }

    /**
     * Return an instance with the specified password information.
     *
     * This method MUST retain the state of the current instance and return
     * an instance that contains the specified password information.
     *
     * @api
     * @param \Stringable|non-empty-string|null $password
     * @throws InvalidUriComponentArgumentException in case of invalid password argument passed
     */
    final public function withPassword(#[\SensitiveParameter] \Stringable|string|null $password): static
    {
        $self = clone $this;
        $self->password = $this->formatPasswordParameter($password);

        return $self;
    }

    /**
     * Return an instance without password information.
     *
     * This method MUST retain the state of the current instance and return
     * an instance that did not contain password information.
     *
     * @api
     */
    final public function withoutPassword(): self
    {
        $self = clone $this;
        $self->password = null;

        return $self;
    }

    /**
     * Return an instance with the specified password information.
     *
     * This method MUST retain the state of the current instance and return
     * an instance that contains the specified password information.
     *
     * @api
     * @param \Stringable|non-empty-string $user
     * @param \Stringable|non-empty-string|null $password
     * @throws InvalidUriComponentArgumentException in case of invalid username
     *         or password argument passed
     */
    final public function withCredentials(
        \Stringable|string $user,
        #[\SensitiveParameter] \Stringable|string|null $password = null,
    ): static {
        $self = clone $this;
        $self->user = $this->formatUserParameter($user);
        $self->password = $this->formatPasswordParameter($password);

        return $self;
    }

    /**
     * Format user parameter
     *
     * @return non-empty-string
     * @throws InvalidUriComponentArgumentException in case of invalid username argument passed
     */
    protected function formatUserParameter(\Stringable|string $user): string
    {
        if ($user instanceof \Stringable) {
            try {
                $user = (string) $user;
                /** @phpstan-ignore-next-line : This is not a dead catch */
            } catch (\Throwable $e) {
                throw InvalidUriComponentArgumentException::becauseStringableErrorOccurs($e);
            }
        }

        if ($user === '') {
            throw InvalidUriComponentArgumentException::becauseComponentIsEmpty('user');
        }

        return $user;
    }

    /**
     * Format password parameter
     *
     * @return non-empty-string|null
     * @throws InvalidUriComponentArgumentException in case of invalid password argument passed
     */
    protected function formatPasswordParameter(#[\SensitiveParameter] \Stringable|string|null $password): ?string
    {
        if ($password instanceof \Stringable) {
            try {
                $password = (string) $password;
                /** @phpstan-ignore-next-line : This is not a dead catch */
            } catch (\Throwable $e) {
                throw InvalidUriComponentArgumentException::becauseStringableErrorOccurs($e);
            }
        }

        if ($password === '') {
            return null;
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
