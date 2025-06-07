<?php

declare(strict_types=1);

namespace Boson\Window\Api;

use Boson\Api\Extension;
use Boson\Window\Window;
use FFI\CData;

/**
 * @template-extends Extension<Window>
 */
abstract class WindowExtension extends Extension
{
    protected function getHandle(object $context): CData
    {
        return $context->id->ptr;
    }
}
