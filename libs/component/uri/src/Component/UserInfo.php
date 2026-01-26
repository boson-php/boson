<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidUriComponentArgumentException;
use Boson\Contracts\Uri\Component\UserInfoInterface;

/**
 * @phpstan-sealed MutableUserInfo
 */
class UserInfo implements UserInfoInterface
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public protected(set) string $user,
        /**
         * @var non-empty-string|null
         */
        #[\SensitiveParameter]
        public protected(set) ?string $password = null,
    ) {}

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
    public function withUser(\Stringable|string $user): static
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
    public function withPassword(#[\SensitiveParameter] \Stringable|string|null $password): static
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
    public function withoutPassword(): self
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
    public function withCredentials(
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
            } catch (\Throwable $e) {
                throw InvalidUriComponentArgumentException::becauseStringableErrorOccurs($e);
            }
        }

        if ($password === '') {
            return null;
        }

        return $password;
    }

    public function equals(mixed $other): bool
    {
        return $other === $this
            || ($other instanceof UserInfoInterface
                && $other->user === $this->user
                && $other->password === $this->password);
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
