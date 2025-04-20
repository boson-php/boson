<?php

declare(strict_types=1);

namespace Boson\WebView\Event;

use Boson\Dispatcher\Event;
use Boson\WebView\WebView;

/**
 * @template-extends Event<WebView>
 */
abstract class WebViewEvent extends Event
{
    public function __construct(WebView $subject, ?int $time = null)
    {
        parent::__construct($subject, $time);
    }
}
