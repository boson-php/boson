<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class AsAttribute
{
    public function __construct(
        /**
         * @var non-empty-string|null
         */
        public ?string $name = null,
        public bool $enableHooks = false,
        public bool $renderAfterCall = true,
    ) {}
}
