<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Instantiator;

use Boson\WebView\Api\WebComponentsApi\ReactiveElementContext;

interface WebComponentInstantiatorInterface
{
    /**
     * @param ReactiveElementContext<object> $context
     */
    public function create(ReactiveElementContext $context): object;
}
