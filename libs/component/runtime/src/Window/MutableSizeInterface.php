<?php

declare(strict_types=1);

namespace Boson\Window;

interface MutableSizeInterface extends SizeInterface
{
    /**
     * See {@see SizeInterface::$width} and also allows to update this value.
     *
     * @var int<0, 2147483647>
     */
    public int $width { get; set; }

    /**
     * See {@see SizeInterface::$height} and also allows to update this value.
     *
     * @var int<0, 2147483647>
     */
    public int $height { get; set; }

    /**
     * Atomically (simultaneously) update the width and height of the desired
     * content area in screen coordinates.
     *
     * @param int<0, 2147483647>|null $width
     * @param int<0, 2147483647>|null $height
     */
    public function update(?int $width = null, ?int $height = null): void;
}
