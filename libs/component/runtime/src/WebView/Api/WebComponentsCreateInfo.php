<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\WebView\Api\WebComponents\Instantiator\SimpleWebComponentInstantiator;
use Boson\WebView\Api\WebComponents\Instantiator\WebComponentInstantiatorInterface;

final readonly class WebComponentsCreateInfo
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_CLASS_PREFIX = 'BosonWebComponent$';

    public function __construct(
        /**
         * Prefix for web component classes
         */
        public string $classNamePrefix = self::DEFAULT_CLASS_PREFIX,
        /**
         * Contain instantiator of components for Web Components API.
         */
        public WebComponentInstantiatorInterface $instantiator = new SimpleWebComponentInstantiator(),
    ) {}
}
