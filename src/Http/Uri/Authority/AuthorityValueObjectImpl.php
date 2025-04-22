<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Authority;

use Boson\Http\Uri\AuthorityInterface;

/**
 * @phpstan-require-implements AuthorityInterface
 */
trait AuthorityValueObjectImpl
{
    use AuthorityStringableImpl;

    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract public ?UserInfoInterface $userInfo { get; }
    // abstract public string $host { get; }
    // abstract public ?int $port { get; }
    //

    public function equals(mixed $object): bool
    {
        return $object === $this
            || ($object instanceof static
                && $this->host === $object->host
                && $this->port === $object->port
                && ($object->userInfo === $this->userInfo
                    || $object->userInfo?->equals($this->userInfo) === true));
    }

    public function toString(): string
    {
        return (string) $this;
    }
}
