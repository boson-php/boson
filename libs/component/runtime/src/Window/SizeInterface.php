<?php

declare(strict_types=1);

namespace Boson\Window;

interface SizeInterface extends \Stringable
{
    /**
     * Gets desired width, in screen coordinates, of the content area.
     *
     * @var int<0, 2147483647>
     */
    public int $width { get; }

    /**
     * Gets desired height, in screen coordinates, of the content area.
     *
     * @var int<0, 2147483647>
     */
    public int $height { get; }

    /**
     * String representation of the {@see SizeInterface} object.
     *
     * @return non-empty-string
     */
    public function __toString(): string;
}
