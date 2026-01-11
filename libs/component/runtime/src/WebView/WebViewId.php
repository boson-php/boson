<?php

declare(strict_types=1);

namespace Boson\WebView;

use Boson\Component\Saucer\SaucerInterface;
use Boson\Internal\StructPointerId;
use FFI\CData;

final readonly class WebViewId extends StructPointerId
{
    /**
     * Returns new {@see WebViewId} instance from given `saucer_webview*` struct pointer.
     *
     * @api
     */
    final public static function fromHandle(SaucerInterface $api, CData $handle): self
    {
        $id = self::getPointerIntValue($api, $handle);

        return new self($id, $handle);
    }
}
