<?php

declare(strict_types=1);

namespace Boson\WebView\Event;

use Boson\Shared\Marker\AsWebViewEvent;
use Boson\WebView\WebView;

#[AsWebViewEvent]
final class WebViewNavigated extends WebViewEvent
{
    public function __construct(
        WebView $subject,
        /**
         * @var non-empty-string
         */
        public readonly string $url,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
