<?php

declare(strict_types=1);

namespace Boson\Window;

use Boson\Window\Size\SizeStringableProvider;

final readonly class Size implements SizeInterface
{
    use SizeStringableProvider;

    public function __construct(
        /**
         * @var int<0, 2147483647>
         */
        public int $width = 0,
        /**
         * @var int<0, 2147483647>
         */
        public int $height = 0,
    ) {
        assert($width >= 0 && $width <= 2147483647, new \InvalidArgumentException(
            message: 'Width CAN NOT be less than 0 or greater than 2147483647',
        ));

        assert($height >= 0 && $height <= 2147483647, new \InvalidArgumentException(
            message: 'Height CAN NOT be less than 0 or greater than 2147483647',
        ));
    }
}
