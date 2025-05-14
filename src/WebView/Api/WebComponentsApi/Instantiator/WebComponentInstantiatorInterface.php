<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Instantiator;

use Boson\WebView\Api\WebComponentsApi\ReactiveElement;

interface WebComponentInstantiatorInterface
{
    /**
     * @param ReactiveElement<object> $context
     */
    public function create(ReactiveElement $context): object;
}
