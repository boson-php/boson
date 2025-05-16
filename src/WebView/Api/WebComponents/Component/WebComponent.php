<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Component;

use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Api\Data\AsyncDataRetrieverInterface;
use Boson\WebView\Api\Data\SyncDataRetrieverInterface;
use Boson\WebView\Api\Scripts\ScriptEvaluatorInterface;
use Boson\WebView\Api\WebComponents\Context\AttributeMapInterface;
use Boson\WebView\Api\WebComponents\Context\MutableAttributeMapInterface;
use Boson\WebView\Api\WebComponents\Context\MutableClassListInterface;
use Boson\WebView\Api\WebComponents\Context\MutableContentProviderInterface;
use Boson\WebView\Api\WebComponents\Context\ReactiveContext;
use Boson\WebView\WebView;
use JetBrains\PhpStorm\Language;
use React\Promise\PromiseInterface;

abstract class WebComponent implements
    HasClassNameInterface,
    HasObservedAttributesInterface,
    HasMethodsInterface,
    HasLifecycleCallbacksInterface,
    HasTemplateInterface,
    ScriptEvaluatorInterface,
    SyncDataRetrieverInterface,
    AsyncDataRetrieverInterface
{
    public readonly MutableAttributeMapInterface $attributes;

    public readonly MutableClassListInterface $classList;

    public readonly MutableContentProviderInterface $shadowRoot;

    /**
     * @var non-empty-lowercase-string
     */
    public readonly string $tagName;

    /**
     * @api
     *
     * @link https://developer.mozilla.org/docs/Web/API/Element/innerHTML
     *
     * @uses MutableContentProviderInterface::$html
     */
    public string $innerHtml {
        get => $this->content->html;
        set(string|\Stringable $html) {
            $this->content->html = $html;
        }
    }

    /**
     * @api
     *
     * @link https://developer.mozilla.org/docs/Web/API/Node/textContent
     *
     * @uses MutableContentProviderInterface::$text
     */
    public string $textContent {
        get => $this->content->text;
        set(string|\Stringable $text) {
            $this->content->text = $text;
        }
    }

    public function __construct(
        /**
         * @var ReactiveContext<$this>
         */
        private readonly ReactiveContext $ctx,
        protected readonly WebView $webview,
    ) {
        $this->tagName = $ctx->name;
        $this->attributes = $ctx->attributes;
        $this->classList = $ctx->classList;
        $this->shadowRoot = $ctx->shadow;
    }

    /**
     * @api
     *
     * @link https://developer.mozilla.org/docs/Web/API/Element/hasAttribute
     *
     * @uses AttributeMapInterface::has()
     *
     * @param non-empty-string $name
     */
    public function hasAttribute(string $name): bool
    {
        return $this->attributes->has($name);
    }

    /**
     * @api
     *
     * @link https://developer.mozilla.org/docs/Web/API/Element/hasAttributes
     *
     * @uses AttributeMapInterface::count()
     */
    public function hasAttributes(): bool
    {
        return $this->attributes->count() > 0;
    }

    /**
     * @api
     *
     * @link https://developer.mozilla.org/docs/Web/API/Element/removeAttribute
     *
     * @uses MutableAttributeMapInterface::remove()
     *
     * @param non-empty-string $name
     */
    public function removeAttribute(string $name): void
    {
        $this->attributes->remove($name);
    }

    /**
     * @api
     *
     * @link https://developer.mozilla.org/docs/Web/API/Element/getAttribute
     *
     * @uses MutableAttributeMapInterface::get()
     *
     * @param non-empty-string $name
     */
    public function getAttribute(string $name): ?string
    {
        return $this->attributes->get($name);
    }

    /**
     * @api
     *
     * @link https://developer.mozilla.org/docs/Web/API/Element/setAttribute
     *
     * @uses MutableAttributeMapInterface::set()
     *
     * @param non-empty-string $name
     */
    public function setAttribute(string $name, string $value): void
    {
        $this->attributes->set($name, $value);
    }

    public static function getClassName(): string
    {
        $name = \str_replace('\\', '_', static::class);

        return 'BosonWebComponent$' . $name;
    }

    public function onConnect(): void
    {
        // Can be overridden
    }

    public function onDisconnect(): void
    {
        // Can be overridden
    }

    public function onAttributeChanged(string $attribute, ?string $value, ?string $previous): void
    {
        $this->refresh();
    }

    public static function getObservedAttributeNames(): array
    {
        // Can be overridden
        return [];
    }

    public function onMethodCalled(string $method, array $args = []): mixed
    {
        $this->refresh();

        return null;
    }

    public static function getMethodNames(): array
    {
        // Can be overridden
        return [];
    }

    public function render(): string
    {
        if ($this instanceof \Stringable) {
            return (string) $this;
        }

        // Can be overridden
        return '<slot />';
    }

    protected function refresh(): void
    {
        if ($this instanceof HasShadowDomInterface) {
            $this->ctx->shadow->html = $this->render();
        } else {
            $this->ctx->content->html = $this->render();
        }
    }

    public function eval(#[Language('JavaScript')] string $code): void
    {
        $this->ctx->eval($code);
    }

    public function defer(#[Language('JavaScript')] string $code): PromiseInterface
    {
        return $this->ctx->defer($code);
    }

    #[BlockingOperation]
    public function get(#[Language('JavaScript')] string $code, ?float $timeout = null): mixed
    {
        return $this->ctx->get($code, $timeout);
    }
}
