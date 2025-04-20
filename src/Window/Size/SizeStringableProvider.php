<?php

declare(strict_types=1);

namespace Boson\Window\Size;

use Boson\Window\SizeInterface;

/**
 * @phpstan-require-implements SizeInterface
 * @mixin SizeInterface
 */
trait SizeStringableProvider
{
    public function __toString(): string
    {
        return \vsprintf('Size(%d × %d)', [
            $this->width,
            $this->height,
        ]);
    }
}
