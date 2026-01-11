<?php

declare(strict_types=1);

namespace Boson\Window;

use Boson\Component\Saucer\SaucerInterface;
use Boson\Shared\StructPointerId;
use FFI\CData;

final readonly class WindowId extends StructPointerId
{
    /**
     * Returns new {@see WindowId} instance from given `saucer_window*` struct pointer.
     *
     * @api
     */
    final public static function fromHandle(SaucerInterface $api, CData $handle): self
    {
        $id = self::getPointerIntValue($api, $handle);

        return new self($id, $handle);
    }
}
