<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Instantiator;

use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentMetadata;

interface WebComponentInstantiatorInterface
{
    /**
     * @template TArgComponent of object
     *
     * @param WebComponentMetadata<TArgComponent> $component
     * @return TArgComponent
     */
    public function create(WebComponentMetadata $component): object;
}
