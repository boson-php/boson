<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidHostArgumentException;
use Boson\Component\Uri\Exception\InvalidPasswordArgumentException;
use Boson\Component\Uri\Exception\InvalidPortArgumentException;
use Boson\Component\Uri\Exception\InvalidUserArgumentException;
use Boson\Contracts\Uri\Component\AuthorityInterface;
use Boson\Contracts\Uri\Component\UserInfoInterface;
use Boson\Contracts\Uri\Exception\InvalidArgumentExceptionInterface;

/**
 * @phpstan-sealed MutableAuthority
 *
 * @phpstan-consistent-constructor
 */
class Authority implements AuthorityInterface
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

    public protected(set) string $host;

    public protected(set) ?int $port;

    /**
     * Gets the userinfo URI component with a specific {@see UserInfo}
     * implementation.
     *
     * @var UserInfo|null
     */
    public protected(set) ?UserInfoInterface $userInfo;

    /**
     * @param \Stringable|non-empty-string $host
     * @param int<0, 65535>|null $port
     *
     * @throws InvalidHostArgumentException if an invalid authority host is provided
     * @throws InvalidPortArgumentException if an invalid authority port is provided
     * @throws InvalidUserArgumentException if an invalid user info's username is provided
     * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
     */
    public function __construct(
        \Stringable|string $host,
        ?int $port = null,
        ?UserInfoInterface $info = null,
    ) {
        $this->host = $this->formatHost($host);
        $this->port = $this->formatPort($port);
        $this->userInfo = $this->formatUserInfo($info);
    }

    /**
     * Returns an authority instance from another one
     *
     * @api
     *
     * @throws InvalidHostArgumentException if an invalid authority host is provided
     * @throws InvalidPortArgumentException if an invalid authority port is provided
     * @throws InvalidUserArgumentException if an invalid user info's username is provided
     * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
     */
    final public static function from(AuthorityInterface $authority): static
    {
        if ($authority instanceof static) {
            return clone $authority;
        }

        return new static(
            host: $authority->host,
            port: $authority->port,
            info: $authority->userInfo,
        );
    }

    /**
     * Returns an authority instance from another one
     *
     * @api
     *
     * @throws InvalidHostArgumentException if an invalid authority host is provided
     * @throws InvalidPortArgumentException if an invalid authority port is provided
     * @throws InvalidUserArgumentException if an invalid user info's username is provided
     * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
     */
    final public static function tryFrom(?AuthorityInterface $authority): ?static
    {
        if ($authority === null) {
            return null;
        }

        return static::from($authority);
    }

    /**
     * @throws InvalidUserArgumentException if an invalid user info's username is provided
     * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
     */
    final public function withUserInfo(?UserInfoInterface $info): static
    {
        $self = clone $this;
        $self->userInfo = $this->formatUserInfo($info);

        return $self;
    }

    /**
     * Return an instance without user info information.
     *
     * This method MUST retain the state of the current instance and return
     * an instance that did not contain user info information.
     *
     * @api
     */
    final public function withoutUserInfo(): static
    {
        $self = clone $this;
        $self->userInfo = null;

        return $self;
    }

    /**
     * @throws InvalidHostArgumentException if an invalid authority host is provided
     */
    final public function withHost(\Stringable|string $host): static
    {
        $self = clone $this;
        $self->host = $this->formatHost($host);

        return $self;
    }

    /**
     * @throws InvalidPortArgumentException if an invalid authority port is provided
     */
    final public function withPort(?int $port): static
    {
        $self = clone $this;
        $self->port = $this->formatPort($port);

        return $self;
    }

    /**
     * Return an instance without port information.
     *
     * This method MUST retain the state of the current instance and return
     * an instance that did not contain port information.
     *
     * @api
     */
    final public function withoutPort(): static
    {
        $self = clone $this;
        $self->port = null;

        return $self;
    }

    /**
     * A facade method of the {@see Authority::withUser()}
     *
     * @api
     *
     * @param non-empty-string|\Stringable $user
     *
     * @throws InvalidUserArgumentException if an invalid user info's username is provided
     * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
     * @throws InvalidArgumentExceptionInterface in case of other validation errors
     */
    final public function withUser(\Stringable|string $user): static
    {
        $self = clone $this;

        if ($self->userInfo === null) {
            $self->userInfo = new UserInfo($user);

            return $self;
        }

        $self->userInfo = $self->userInfo->withUser($user);

        return $self;
    }

    /**
     * A facade method of the {@see Authority::withPassword()}
     *
     * @api
     *
     * @param \Stringable|non-empty-string|null $password
     *
     * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
     * @throws InvalidArgumentExceptionInterface in case of other validation errors
     */
    final public function withPassword(#[\SensitiveParameter] \Stringable|string|null $password): static
    {
        $self = clone $this;

        if ($self->userInfo === null) {
            if ($password === null) {
                return $self;
            }

            throw InvalidPasswordArgumentException::becauseUserNotDefined();
        }

        $self->userInfo = $self->userInfo->withPassword($password);

        return $self;
    }

    /**
     * A facade method of the {@see Authority::withoutPassword()}
     *
     * @api
     */
    final public function withoutPassword(): self
    {
        $self = clone $this;

        if ($self->userInfo === null) {
            return $self;
        }

        $self->userInfo = $self->userInfo->withoutPassword();

        return $self;
    }

    /**
     * A facade method of the {@see Authority::withCredentials()}
     *
     * @api
     *
     * @param \Stringable|non-empty-string $user
     * @param \Stringable|non-empty-string|null $password
     *
     * @throws InvalidUserArgumentException if an invalid user info's username is provided
     * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
     */
    final public function withCredentials(
        \Stringable|string $user,
        #[\SensitiveParameter]
        \Stringable|string|null $password = null,
    ): static {
        $self = clone $this;

        if ($self->userInfo === null) {
            $self->userInfo = new UserInfo($user, $password);

            return $self;
        }

        $self->userInfo = $self->userInfo->withCredentials($user, $password);

        return $self;
    }

    /**
     * @return non-empty-string
     * @throws InvalidHostArgumentException if an invalid authority host is provided
     */
    protected function formatHost(\Stringable|string $host): string
    {
        if ($host instanceof \Stringable) {
            try {
                $host = (string) $host;
                /** @phpstan-ignore-next-line : This is not a dead catch */
            } catch (\Throwable $e) {
                throw InvalidHostArgumentException::becauseStringableErrorOccurs($e);
            }
        }

        if ($host === '') {
            throw InvalidHostArgumentException::becauseComponentIsEmpty();
        }

        return $host;
    }

    /**
     * @return int<0, 65535>|null
     * @throws InvalidPortArgumentException if an invalid authority port is provided
     */
    protected function formatPort(?int $port): ?int
    {
        if ($port === null) {
            return null;
        }

        if ($port > 65535 || $port < 0) {
            throw InvalidPortArgumentException::becauseComponentMustBe('int<0, 65535>', $port);
        }

        return $port;
    }

    /**
     * @throws InvalidUserArgumentException if an invalid user info's username is provided
     * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
     */
    protected function formatUserInfo(?UserInfoInterface $info): ?UserInfo
    {
        return UserInfo::tryFrom($info);
    }

    public function equals(mixed $other): bool
    {
        return $other === $this
            || ($other instanceof AuthorityInterface
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
