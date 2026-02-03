<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidHostArgumentException;
use Boson\Component\Uri\Exception\InvalidPasswordArgumentException;
use Boson\Component\Uri\Exception\InvalidPortArgumentException;
use Boson\Component\Uri\Exception\InvalidUserArgumentException;
use Boson\Contracts\Uri\Component\AuthorityInterface;
use Boson\Contracts\Uri\Component\UserInfoInterface;

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
        get => $this->userInfo?->username;
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
     * @param int<0, max>|null $port
     *
     * @throws InvalidHostArgumentException if an invalid authority host is provided
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
     * @throws InvalidUserArgumentException if an invalid user info's username is provided
     * @throws InvalidPasswordArgumentException if an invalid user info's password is provided
     */
    final public static function from(AuthorityInterface $authority): static
    {
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
     * @return int<0, max>|null
     */
    protected function formatPort(?int $port): ?int
    {
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

    final public function equals(mixed $other): bool
    {
        return $other === $this
            || ($other instanceof AuthorityInterface
                && $this->host === $other->host
                && $this->port === $other->port
                && ($other->userInfo === $this->userInfo
                    || $other->userInfo?->equals($this->userInfo) === true));
    }

    final public function toString(): string
    {
        return (string) $this;
    }

    final public function __toString(): string
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
