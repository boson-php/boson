<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Instantiator;

use Boson\WebView\Api\WebComponentsApi\ReactiveElement;

interface WebComponentInstantiatorInterface
{
    /**
     * @template TArgComponent of object
     *
     * @param ReactiveElement<TArgComponent> $context
     *
     * @return TArgComponent
     */
    public function create(ReactiveElement $context): object;
}
