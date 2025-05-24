<?php

declare(strict_types=1);

namespace Boson\Window\Internal\Size;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Window
 */
abstract class MemoizedManagedSize extends ManagedSize
{
    /**
     * @var int<0, 2147483647>
     */
    public int $width {
        /** @phpstan-ignore-next-line : PHPStan does not support PHP 8.4 properties */
        get => $this->width ??= parent::$width::get();
        /** @phpstan-ignore-next-line : PHPStan does not support PHP 8.4 properties */
        set { parent::$width::set($this->width = $value); }
    }

    /**
     * @var int<0, 2147483647>
     */
    public int $height {
        /** @phpstan-ignore-next-line : PHPStan does not support PHP 8.4 properties */
        get => $this->height ??= parent::$height::get();
        /** @phpstan-ignore-next-line : PHPStan does not support PHP 8.4 properties */
        set { parent::$height::set($this->height = $value); }
    }
}
