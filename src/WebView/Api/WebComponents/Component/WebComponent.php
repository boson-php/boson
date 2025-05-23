<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Component;

use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Api\Data\AsyncDataRetrieverInterface;
use Boson\WebView\Api\Data\SyncDataRetrieverInterface;
use Boson\WebView\Api\Scripts\ScriptEvaluatorInterface;
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
    HasEventListenersInterface,
    HasLifecycleCallbacksInterface,
    HasTemplateInterface,
    ScriptEvaluatorInterface,
    SyncDataRetrieverInterface,
    AsyncDataRetrieverInterface
{
    use HasTemplate;
    use HasClassName;
    use HasObservedAttributes;

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
        get => $this->ctx->content->html;
        set(string|\Stringable $html) {
            $this->ctx->content->html = $html;
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
        get => $this->ctx->content->text;
        set(string|\Stringable $text) {
            $this->ctx->content->text = $text;
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

    public function onConnect(): void
    {
        // Can be overridden
    }

    public function onDisconnect(): void
    {
        // Can be overridden
    }

    public function onEventFired(string $event, array $arguments = []): void
    {
        // Can be overridden
    }

    public static function getEventListeners(): array
    {
        // Can be overridden
        return [];
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
