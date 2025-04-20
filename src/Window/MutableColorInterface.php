<?php

declare(strict_types=1);

namespace Boson\Window;

interface MutableColorInterface extends ColorInterface
{
    /**
     * @param int<0, 255>|null $red
     * @param int<0, 255>|null $green
     * @param int<0, 255>|null $blue
     * @param int<0, 255>|null $alpha
     */
    public function update(?int $red = null, ?int $green = null, ?int $blue = null, ?int $alpha = null): void;
}
