<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Instantiator;

use Boson\WebView\Api\WebComponents\Context\ReactiveContext;
use Boson\WebView\WebView;

interface WebComponentInstantiatorInterface
{
    /**
     * @param ReactiveContext<object> $context
     */
    public function create(WebView $webview, ReactiveContext $context): object;
}
