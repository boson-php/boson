<?php

declare(strict_types=1);

namespace Boson\WebView\Scripts;

use FFI\CData;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Shared\Id\StructPointerId;

final readonly class WebViewScriptId extends StructPointerId
{
    /**
     * Returns new {@see WebViewScriptId} instance from given
     * `saucer_script*` struct pointer.
     *
     * @api
     */
    final public static function fromScriptHandle(LibSaucer $api, CData $handle): self
    {
        $id = self::getPointerIntValue($api, $handle);

        return new self($id, $handle);
    }
}
