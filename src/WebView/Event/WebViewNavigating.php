<?php

declare(strict_types=1);

namespace Boson\WebView\Event;

use Boson\Shared\Marker\AsWebViewEvent;
use Boson\WebView\WebView;

#[AsWebViewEvent]
final class WebViewNavigating extends WebViewIntention
{
    public function __construct(
        WebView $subject,
        /**
         * @var non-empty-string
         */
        public readonly string $url,
        public readonly bool $isNewWindow,
        public readonly bool $isRedirection,
        public readonly bool $isUserInitiated,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
