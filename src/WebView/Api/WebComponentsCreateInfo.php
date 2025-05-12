<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\Shared\PropertyAccessor\PropertyAccessorInterface;
use Boson\Shared\PropertyAccessor\ReflectionPropertyAccessor;
use Boson\WebView\Api\WebComponentsApi\Instantiator\SimpleWebComponentInstantiator;
use Boson\WebView\Api\WebComponentsApi\Instantiator\WebComponentInstantiatorInterface;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\AttributeWebComponentsMetadataReader;
use Boson\WebView\Api\WebComponentsApi\Metadata\Reader\WebComponentMetadataReaderInterface;

final readonly class WebComponentsCreateInfo
{
    public function __construct(
        /**
         * Contain instantiator of components for Web Components API.
         */
        public WebComponentInstantiatorInterface $instantiator = new SimpleWebComponentInstantiator(),
        /**
         * Contain components metadata reader for Web Components API.
         */
        public WebComponentMetadataReaderInterface $metadataReader = new AttributeWebComponentsMetadataReader(),
        /**
         * Contain objects property accessor.
         */
        public PropertyAccessorInterface $propertyAccessor = new ReflectionPropertyAccessor(),
    ) {}
}
