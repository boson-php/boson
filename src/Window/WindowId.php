<?php

declare(strict_types=1);

namespace Boson\Window;

use FFI\CData;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Shared\Id\StructPointerId;

final readonly class WindowId extends StructPointerId
{
    /**
     * Returns new {@see WindowId} instance from given
     * `saucer_handle*` struct pointer.
     *
     * @api
     */
    final public static function fromHandle(LibSaucer $api, CData $handle): self
    {
        $id = self::getPointerIntValue($api, $handle);

        return new self($id, $handle);
    }
}
