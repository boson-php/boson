<?php

declare(strict_types=1);

namespace Boson\Http\Headers;

use Boson\Http\Headers\HeaderLine\HeaderLineStringableImpl;

abstract class HeaderLine implements HeaderLineInterface
{
    use HeaderLineStringableImpl;

    public function __construct(
        /**
         * Gets raw header line content as string.
         */
        protected readonly string $rawHeaderValueString,
    ) {}

    public function __toString(): string
    {
        return $this->rawHeaderValueString;
    }
}
