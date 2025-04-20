<?php

declare(strict_types=1);

namespace Boson\Window\Color;

use Boson\Window\ColorInterface;

/**
 * @phpstan-require-implements ColorInterface
 * @mixin ColorInterface
 */
trait ColorStringableProvider
{
    public function __toString(): string
    {
        if ($this->alpha === 255) {
            return \vsprintf('#%02x%02x%02x', [
                $this->red,
                $this->green,
                $this->blue,
            ]);
        }

        return \vsprintf('#%02x%02x%02x%02x', [
            $this->red,
            $this->green,
            $this->blue,
            $this->alpha,
        ]);
    }
}
