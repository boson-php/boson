<?php

declare(strict_types=1);

namespace Boson\Window\Color;

use Boson\Window\ColorInterface;

interface ColorFactoryInterface
{
    /**
     * Creates color from string value.
     *
     * @param non-empty-string $color
     */
    public function createFromString(string $color): ?ColorInterface;
}
