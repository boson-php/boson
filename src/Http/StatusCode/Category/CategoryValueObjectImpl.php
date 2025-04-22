<?php

declare(strict_types=1);

namespace Boson\Http\StatusCode\Category;

use Boson\Http\StatusCode\CategoryInterface;

/**
 * @phpstan-require-implements CategoryInterface
 */
trait CategoryValueObjectImpl
{
    use CategoryStringableImpl;

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
