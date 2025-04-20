<?php

declare(strict_types=1);

namespace Boson\Window;

interface ColorInterface extends \Stringable
{
    /**
     * The red component of the color
     *
     * @var int<0, 255>
     */
    public int $red { get; }

    /**
     * The red component of the color
     *
     * @var int<0, 255>
     */
    public int $green { get; }

    /**
     * The red component of the color
     *
     * @var int<0, 255>
     */
    public int $blue { get; }

    /**
     * The red component of the color
     *
     * @var int<0, 255>
     */
    public int $alpha { get; }
}
