<?php

declare(strict_types=1);

namespace Boson\WebView\Event;

use Boson\Dispatcher\Intention;
use Boson\WebView\WebView;

/**
 * @template-extends Intention<WebView>
 */
abstract class WebViewIntention extends Intention
{
    public function __construct(WebView $subject, ?int $time = null)
    {
        parent::__construct($subject, $time);
    }
}
