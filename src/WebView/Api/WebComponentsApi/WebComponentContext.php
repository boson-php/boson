<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi;

/**
 * @template TComponent of object
 */
final readonly class WebComponentContext
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public string $name,
        /**
         * @var class-string<TComponent>
         */
        public string $component,
        public AttributeChangerInterface $attributeChanger,
        public TemplateChangerInterface $templateChanger,
    ) {}
}
