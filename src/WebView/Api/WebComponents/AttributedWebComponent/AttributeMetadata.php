<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\AttributedWebComponent;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Api\WebComponentsApi
 */
final readonly class AttributeMetadata
{
    public function __construct(
        /**
         * Contain property reflection
         */
        public \ReflectionProperty $property,
        public bool $enableHooks,
        /**
         * Should refresh template after method call
         */
        public bool $renderAfterCall,
    ) {}
}
