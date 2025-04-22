<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Query;

use Boson\Http\Uri\QueryInterface;

/**
 * @phpstan-require-implements QueryInterface
 */
trait QueryValueObjectImpl
{
    use QueryStringableImpl;

    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract private array $components { get; }
    //

    public function equals(mixed $object): bool
    {
        return $object === $this
            || ($object instanceof static
                && $object->components === $this->components);
    }

    public function toString(): string
    {
        return (string) $this;
    }
}
