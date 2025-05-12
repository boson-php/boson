<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Attribute;

use JetBrains\PhpStorm\Language;

#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class AsWebComponent
{
    public function __construct(
        /**
         * @var non-empty-string|null
         */
        public ?string $tag = null,
        /**
         * @var non-empty-string|null
         */
        public ?string $class = null,
        #[Language('HTML')]
        public ?string $template = null,
    ) {}
}
