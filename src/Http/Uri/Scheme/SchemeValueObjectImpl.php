<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Scheme;

use Boson\Http\Uri\SchemeInterface;

/**
 * @phpstan-require-implements SchemeInterface
 */
trait SchemeValueObjectImpl
{
    use SchemeStringableImpl;

    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract public string $name { get; }
    //

    public function equals(mixed $object): bool
    {
        return $object === $this
            || ($object instanceof static
                && $object->name === $this->name);
    }

    public function toString(): string
    {
        return (string) $this;
    }
}
