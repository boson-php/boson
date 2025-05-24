<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context;

use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Api\Data\DataRetrieverInterface;
use Boson\WebView\Api\Scripts\ScriptEvaluatorInterface;
use JetBrains\PhpStorm\Language;
use React\Promise\PromiseInterface;

/**
 * @template TComponent of object
 */
final readonly class ReactiveContext implements
    ScriptEvaluatorInterface,
    DataRetrieverInterface
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
        /**
         * Gets access to component's attributes list
         */
        public MutableAttributeMapInterface $attributes,
        /**
         * Gets access to component's properties list
         */
        public MutablePropertyMapInterface $properties,
        /**
         * Gets access to component's class list
         */
        public MutableClassListInterface $classList,
        /**
         * Gets access to real component content
         */
        public MutableContentProviderInterface $content,
        /**
         * Gets access to shadow component content
         */
        public MutableContentProviderInterface $shadow,
        /**
         * Component-aware evaluator
         */
        private ScriptEvaluatorInterface $evaluator,
        /**
         * Component-aware data retriever
         */
        private DataRetrieverInterface $retriever,
    ) {}

    public function eval(#[Language('JavaScript')] string $code): void
    {
        $this->evaluator->eval($code);
    }

    public function defer(#[Language('JavaScript')] string $code): PromiseInterface
    {
        return $this->retriever->defer($code);
    }

    #[BlockingOperation]
    public function get(#[Language('JavaScript')] string $code, ?float $timeout = null): mixed
    {
        return $this->retriever->get($code, $timeout);
    }
}
