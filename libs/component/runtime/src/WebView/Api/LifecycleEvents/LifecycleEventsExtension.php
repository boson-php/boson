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
final class LifecycleEventsExtension extends Extension
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): LifecycleEventsListener
    {
        return new LifecycleEventsListener($ctx, $listener);
    }
}
