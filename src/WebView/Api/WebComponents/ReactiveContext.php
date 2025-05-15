<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents;

use Boson\WebView\Api\WebComponents\Element\MutableAttributeMapInterface;
use Boson\WebView\Api\WebComponents\Element\MutableTemplateContainerInterface;

/**
 * @template TComponent of object
 */
final readonly class ReactiveContext
{
    public function __construct(
        /**
         * Gets the HTML-lowercased qualified name.
         *
         * Unlike the `tagName` attribute, it returns not the uppercase,
         * but the lowercase.
         *
         * @link https://developer.mozilla.org/docs/Web/API/Element/tagName
         *
         * @var non-empty-lowercase-string
         */
        public string $name,
        /**
         * Gets component class name.
         *
         * @var class-string<TComponent>
         */
        public string $component,
        public MutableAttributeMapInterface $attributes,
        public MutableTemplateContainerInterface $content,
    ) {}
}
