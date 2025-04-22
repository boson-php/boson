<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Path;

use Boson\Http\Uri\PathInterface;

/**
 * @phpstan-require-implements PathInterface
 */
trait PathValueObjectImpl
{
    use PathStringableImpl;

    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract private array $segments { get; }
    //

    public function equals(mixed $object): bool
    {
        return $object === $this
            || ($object instanceof static
                && $object->segments === $this->segments);
    }

    public function toString(): string
    {
        return (string) $this;
    }
}
