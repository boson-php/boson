<?php

declare(strict_types=1);

namespace Boson\WebView;

use Boson\Shared\ValueObject\Id\StructPointerId;
use Boson\Window\WindowId;

final readonly class WebViewId extends StructPointerId
{
    /**
     * @api
     */
    final public static function fromWindowId(WindowId $id): self
    {
        return new self($id->id, $id->ptr);
    }
}
