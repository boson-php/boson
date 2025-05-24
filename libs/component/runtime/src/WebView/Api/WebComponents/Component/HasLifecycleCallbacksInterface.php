<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Component;

interface HasLifecycleCallbacksInterface
{
    /**
     * Called each time the element is added to the document. The specification
     * recommends that, as far as possible, developers should implement custom
     * element setup in this callback rather than the constructor.
     */
    public function onConnect(): void;

    /**
     * Called each time the element is removed from the document.
     */
    public function onDisconnect(): void;
}
