<?php

declare(strict_types=1);

namespace Boson\Api;

use Boson\Application;
use FFI\CData;

/**
 * @template-extends Extension<Application>
 */
abstract class ApplicationExtension extends Extension
{
    protected function getHandle(object $context): CData
    {
        return $context->id->ptr;
    }
}
