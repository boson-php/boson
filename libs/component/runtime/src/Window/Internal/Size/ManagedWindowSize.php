<?php

declare(strict_types=1);

namespace Boson\Window\Internal\Size;

use FFI\CData;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Window
 */
final class ManagedWindowSize extends ManagedSize
{
    protected function getCurrentSizeValuesByRef(CData $width, CData $height): void
    {
        $this->api->saucer_window_size($this->handle, $width, $height);
    }

    protected function setSizeValues(int $width, int $height): void
    {
        $this->api->saucer_window_set_size($this->handle, $width, $height);
    }
}
