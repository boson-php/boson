<?php

declare(strict_types=1);

namespace Boson\Window;

use Boson\Window\Color\ColorStringableImpl;

final readonly class Color implements ColorInterface
{
    use ColorStringableImpl;

    public function __construct(
        /**
         * @var int<0, 255>
         */
        public int $red = 255,
        /**
         * @var int<0, 255>
         */
        public int $green = 255,
        /**
         * @var int<0, 255>
         */
        public int $blue = 255,
        /**
         * @var int<0, 255>
         */
        public int $alpha = 255,
    ) {
        assert($red >= 0 && $red <= 255, new \InvalidArgumentException(
            message: 'Red component CAN NOT be less than 0 or greater than 255',
        ));

        assert($green >= 0 && $green <= 255, new \InvalidArgumentException(
            message: 'Green component CAN NOT be less than 0 or greater than 255',
        ));

        assert($blue >= 0 && $blue <= 255, new \InvalidArgumentException(
            message: 'Blue component CAN NOT be less than 0 or greater than 255',
        ));

        assert($alpha >= 0 && $alpha <= 255, new \InvalidArgumentException(
            message: 'Alpha component CAN NOT be less than 0 or greater than 255',
        ));
    }
}
