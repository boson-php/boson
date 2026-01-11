<?php

declare(strict_types=1);

namespace Boson\Window\Api\LifecycleEvents;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Extension;
use Boson\Window\Window;

/**
 * Creates a new instance of {@see WebViewLifecycleEventsListener} that manages
 * the window's native event handling and bridges them to the Saucer's
 * event system.
 *
 * This method initializes an event handler that translates native window
 * events (like resize, focus, close) into application events that can be
 * handled by the event dispatcher.
 *
 * @template-extends Extension<Window>
 */
final class WindowLifecycleEventsExtension extends Extension
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): WindowLifecycleEventsListener
    {
        return new WindowLifecycleEventsListener($ctx, $listener);
    }
}
