<?php

declare(strict_types=1);

namespace Boson\Contracts\Uri\Component;

interface MutablePathInterface extends PathInterface
{
    public bool $isAbsolute {
        get;
        /**
         * Allows to modify the {@see $isAbsolute} value.
         */
        set;
    }

    public bool $hasTrailingSlash {
        get;
        /**
         * Allows to modify the {@see $hasTrailingSlash} value.
         */
        set;
    }
}
