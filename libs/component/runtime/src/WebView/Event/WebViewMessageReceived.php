<?php

declare(strict_types=1);

namespace Boson\WebView\Event;

use Boson\Shared\Marker\AsWebViewEvent;
use Boson\WebView\WebView;

#[AsWebViewEvent]
final class WebViewMessageReceived extends WebViewEvent
{
    public function __construct(
        WebView $subject,
        public string $message,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }

    public function ack(): void
    {
        $this->stopPropagation();
    }
}
