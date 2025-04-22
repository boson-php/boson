<?php

declare(strict_types=1);

namespace Boson\Http\Uri\UserInfo;

use Boson\Http\Uri\UserInfoInterface;

/**
 * @phpstan-require-implements UserInfoInterface
 */
trait UserInfoValueObjectImpl
{
    use UserInfoStringableImpl;

    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract public string $user { get; }
    // abstract public ?string $password { get; }
    //

    public function equals(mixed $object): bool
    {
        return $object === $this
            || ($object instanceof static
                && $object->user === $this->user
                && $object->password === $this->password)
        ;
    }

    public function toString(): string
    {
        return (string) $this;
    }
}
