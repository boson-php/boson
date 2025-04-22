<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Scheme;

use Boson\Http\Uri\SchemeInterface;

/**
 * @phpstan-require-implements SchemeInterface
 */
trait SchemeStringableImpl
{
    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract public string $name { get; }
    //

    public function __toString(): string
    {
        return $this->name;
    }
}
