<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\WebView\Api\WebComponentsApi\Instantiator\SimpleWebComponentInstantiator;
use Boson\WebView\Api\WebComponentsApi\Instantiator\WebComponentInstantiatorInterface;

final readonly class WebComponentsCreateInfo
{
    public function __construct(
        /**
         * Contain instantiator of components for Web Components API.
         */
        public WebComponentInstantiatorInterface $instantiator = new SimpleWebComponentInstantiator(),
    ) {}
}
