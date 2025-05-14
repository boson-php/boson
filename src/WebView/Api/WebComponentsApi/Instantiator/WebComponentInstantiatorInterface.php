<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Instantiator;

use Boson\WebView\Api\WebComponentsApi\ReactiveContext;
use Boson\WebView\WebView;

interface WebComponentInstantiatorInterface
{
    /**
     * @param ReactiveContext<object> $context
     */
    public function create(WebView $webview, ReactiveContext $context): object;
}
