<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Instantiator;

use Boson\WebView\Api\WebComponentsApi\ReactiveElementContext;
use Boson\WebView\WebView;

interface WebComponentInstantiatorInterface
{
    /**
     * @param ReactiveElementContext<object> $context
     */
    public function create(WebView $webview, ReactiveElementContext $context): object;
}
