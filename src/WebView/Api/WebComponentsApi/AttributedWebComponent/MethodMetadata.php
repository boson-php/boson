<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\AttributedWebComponent;

final readonly class MethodMetadata
{
    public function __construct(
        /**
         * Contain method reflection
         */
        public \ReflectionMethod $method,
        /**
         * Should refresh template after method call
         */
        public bool $renderAfterCall,
    ) {}
}
