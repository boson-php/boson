<?php

declare(strict_types=1);

namespace Boson\Http\Method;

use Boson\Http\MethodInterface;

/**
 * @phpstan-require-implements MethodInterface
 */
trait MethodStringableImpl
{
    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract public string $name { get; }
    //

    /**
     * @return non-empty-string
     *
     * @phpstan-return non-empty-uppercase-string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
