<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Instantiator;

use Boson\WebView\Api\WebComponentsApi\WebComponentContext;

interface WebComponentInstantiatorInterface
{
    /**
     * @template TArgComponent of object
     *
     * @param WebComponentContext<TArgComponent> $context
     *
     * @return TArgComponent
     */
    public function create(WebComponentContext $context): object;
}
