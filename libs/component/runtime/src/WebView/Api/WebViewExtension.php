<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\Api\Extension;
use Boson\WebView\WebView;
use FFI\CData;

/**
 * @template-extends Extension<WebView>
 */
abstract class WebViewExtension extends Extension
{
    protected function getHandle(object $context): CData
    {
        return $context->window->id->ptr;
    }
}
