<?php

declare(strict_types=1);

namespace Boson\Http\Method;

use Boson\Http\MethodInterface;

/**
 * @phpstan-require-implements MethodInterface
 */
trait MethodValueObjectImpl
{
    use MethodStringableImpl;

    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract public string $name { get; }
    //

    public function toString(): string
    {
        return $this->name;
    }

    public function equals(mixed $object): bool
    {
        return $object === $this
            || ($object instanceof static
                && $object->name === $this->name);
    }
}
