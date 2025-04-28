<?php

declare(strict_types=1);

namespace Boson\Http\Headers\HeaderLine;

use Boson\Http\Headers\HeaderLineInterface;

/**
 * @phpstan-require-implements HeaderLineInterface
 *
 * @internal this is an internal library trait, please do not use it in your code
 * @psalm-internal Boson\Http\Headers
 */
trait HeaderLineStringableImpl
{
    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract public string $value { get; }
    //

    public function __toString(): string
    {
        return $this->rawHeaderValueString;
    }
}
