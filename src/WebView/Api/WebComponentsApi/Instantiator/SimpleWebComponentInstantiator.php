<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Instantiator;

use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentMetadata;

final class SimpleWebComponentInstantiator implements WebComponentInstantiatorInterface
{
    public function create(WebComponentMetadata $component): object
    {
        $class = $component->component;

        return new $class();
    }
}
