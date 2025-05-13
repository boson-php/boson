<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Instantiator;

use Boson\WebView\Api\WebComponentsApi\WebComponentContext;

final class SimpleWebComponentInstantiator implements WebComponentInstantiatorInterface
{
    public function create(WebComponentContext $context): object
    {
        $class = $context->component;

        return new $class($context);
    }
}
