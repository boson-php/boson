<?php

declare(strict_types=1);

namespace Boson\WebView\Api\LifecycleEvents;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Extension;
use Boson\WebView\WebView;

/**
 * @template-extends Extension<WebView>
 */
final class WebViewLifecycleEventsExtension extends Extension
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): WebViewLifecycleEventsListener
    {
        return new WebViewLifecycleEventsListener($ctx, $listener);
    }
}
