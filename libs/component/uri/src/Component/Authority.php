<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidUriComponentArgumentException;
use Boson\Contracts\Uri\Component\AuthorityInterface;
use Boson\Contracts\Uri\Component\UserInfoInterface;

/**
 * @phpstan-sealed MutableAuthority
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
     */
    public function __construct(
        \Stringable|string $host,
        ?int $port = null,
        ?UserInfoInterface $info = null,
    ) {
        $this->host = $this->formatHostParameter($host);
        $this->port = $this->formatPortParameter($port);
        $this->userInfo = $this->formatUserInfoParameter($info);
    }

    /**
     * Return an instance with the specified host information.
     *
     * This method MUST retain the state of the current instance and return
     * an instance that contains the specified host information.
     *
     * @api
     * @param non-empty-string|\Stringable $host
     * @throws InvalidUriComponentArgumentException in case of invalid host argument passed
     */
    final public function withHost(\Stringable|string $host): static
    {
        $self = clone $this;
        $self->host = $this->formatHostParameter($host);

        return $self;
    }

    /**
     * Return an instance with the specified port information.
     *
     * This method MUST retain the state of the current instance and return
     * an instance that contains the specified port information.
     *
     * @api
     * @param int<0, 65535>|null $port
     * @throws InvalidUriComponentArgumentException in case of invalid port argument passed
     */
    final public function withPort(?int $port): static
    {
        $self = clone $this;
        $self->port = $this->formatPortParameter($port);

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
     * Return an instance with the specified user info information.
     *
     * This method MUST retain the state of the current instance and return
     * an instance that contains the specified user info information.
     *
     * @api
     * @throws InvalidUriComponentArgumentException in case of invalid user info argument passed
     */
    final public function withUserInfo(?UserInfoInterface $info): static
    {
        $self = clone $this;
        $self->userInfo = $this->formatUserInfoParameter($info);

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
     * A facade method of the {@see Authority::withUser()}
     *
     * @api
     * @param non-empty-string|\Stringable $user
     * @throws InvalidUriComponentArgumentException in case of invalid username argument passed
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
     * @param \Stringable|non-empty-string|null $password
     * @throws InvalidUriComponentArgumentException in case of invalid password argument passed
     */
    final public function withPassword(#[\SensitiveParameter] \Stringable|string|null $password): static
    {
        $self = clone $this;

        if ($self->userInfo === null) {
            if ($password === null) {
                return $self;
            }

            throw InvalidUriComponentArgumentException::becauseInvalidLogic(
                message: 'Cannot set a password for authority without user',
            );
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

        if ($self->userInfo === null) {
            $self->userInfo = new UserInfo($user, $password);

            return $self;
        }

        $self->userInfo = $self->userInfo->withCredentials($user, $password);

        return $self;
    }

    /**
     * Format host parameter
     *
     * @return non-empty-string
     * @throws InvalidUriComponentArgumentException in case of invalid host argument passed
     */
    protected function formatHostParameter(\Stringable|string $host): string
    {
        if ($host instanceof \Stringable) {
            try {
                $host = (string) $host;
                /** @phpstan-ignore-next-line : This is not a dead catch */
            } catch (\Throwable $e) {
                throw InvalidUriComponentArgumentException::becauseStringableErrorOccurs($e);
            }
        }

        if ($host === '') {
            throw InvalidUriComponentArgumentException::becauseComponentIsEmpty('host');
        }

        return $host;
    }

    /**
     * Format port parameter
     *
     * @return int<0, 65535>|null
     * @throws InvalidUriComponentArgumentException in case of invalid port argument passed
     */
    protected function formatPortParameter(?int $port): ?int
    {
        if ($port === null) {
            return null;
        }

        if ($port > 65535 || $port < 0) {
            throw InvalidUriComponentArgumentException::becauseComponentMustBe('port', 'int<0, 65535>', $port);
        }

        return $port;
    }

    /**
     * Format user info component parameter
     *
     * @throws InvalidUriComponentArgumentException in case of invalid user
     *         info argument passed
     */
    protected function formatUserInfoParameter(?UserInfoInterface $info): ?UserInfo
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
