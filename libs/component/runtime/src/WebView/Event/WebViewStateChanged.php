<?php

declare(strict_types=1);

namespace Boson\WebView\Event;

use Boson\WebView\WebView;
use Boson\WebView\WebViewState;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class WebViewStateChanged extends WebViewEvent
{
    public function __construct(
        WebView $subject,
        public WebViewState $state,
        ?int $time = null,
    ) {
        parent::__construct($subject, $time);
    }
}
