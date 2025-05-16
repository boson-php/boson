<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Internal;

use Boson\WebView\Api\WebComponents\Component\HasLifecycleCallbacksInterface;
use Boson\WebView\Api\WebComponents\Component\HasMethodsInterface;
use Boson\WebView\Api\WebComponents\Component\HasObservedAttributesInterface;
use Boson\WebView\Api\WebComponents\Component\HasShadowDomInterface;
use Boson\WebView\Api\WebComponents\Component\HasTemplateInterface;
use Boson\WebView\Api\WebComponents\Context\Internal\AttributeMap\MutableComponentAttributeMap;
use Boson\WebView\Api\WebComponents\Context\Internal\ClassList\MutableComponentClassList;
use Boson\WebView\Api\WebComponents\Context\Internal\ComponentDataRetriever;
use Boson\WebView\Api\WebComponents\Context\Internal\ComponentEvaluator;
use Boson\WebView\Api\WebComponents\Context\Internal\Content\MutableShadowDomContentProvider;
use Boson\WebView\Api\WebComponents\Context\Internal\Content\MutableComponentContentProvider;
use Boson\WebView\Api\WebComponents\Context\ReactiveContext;
use Boson\WebView\Api\WebComponents\Instantiator\WebComponentInstantiatorInterface;
use Boson\WebView\WebView;

/**
 * @internal this is an internal library class, please do not use it in your code.
 * @psalm-internal Boson\WebView\Api\WebComponents
 */
final class WebViewComponentInstances
{
    /**
     * A map containing a link between a client instance ID and PHP component instance.
     *
     * @var array<non-empty-string, object>
     */
    private array $instances = [];

    public function __construct(
        private readonly WebView $webview,
        private readonly WebComponentInstantiatorInterface $instantiator,
    ) {}

    /**
     * @template TArgComponent of object
     *
     * @param non-empty-string $id
     * @param non-empty-string $name
     * @param class-string<TArgComponent> $component
     * @return TArgComponent
     */
    private function createContext(string $id, string $name, string $component): ReactiveContext
    {
        $evaluator = new ComponentEvaluator($id, $this->webview->scripts);
        $retriever = new ComponentDataRetriever($id, $this->webview->data);

        return new ReactiveContext(
            name: \strtolower($name),
            component: $component,
            attributes: new MutableComponentAttributeMap($evaluator, $retriever),
            classList: new MutableComponentClassList($evaluator, $retriever),
            content: new MutableComponentContentProvider($evaluator, $retriever),
            shadow: new MutableShadowDomContentProvider($evaluator, $retriever),
            evaluator: $evaluator,
            retriever: $retriever,
        );
    }

    private function renderRaw(object $component): ?string
    {
        if ($component instanceof HasShadowDomInterface) {
            return null;
        }

        if (!$component instanceof HasTemplateInterface) {
            return null;
        }

        return $component->render();
    }

    private function renderShadowDom(object $component): ?string
    {
        if (!$component instanceof HasShadowDomInterface) {
            return null;
        }

        return $component->render();
    }

    /**
     * @param non-empty-string $id
     * @param non-empty-string $name
     * @param class-string $component
     */
    public function create(string $id, string $name, string $component): ?string
    {
        $this->instances[$id] = $instance = $this->instantiator->create(
            webview: $this->webview,
            context: $this->createContext($id, $name, $component),
        );

        return $this->renderRaw($instance);
    }

    /**
     * @param non-empty-string $id
     */
    public function notifyConnect(string $id): ?string
    {
        $instance = $this->instances[$id] ?? null;

        if ($instance === null) {
            return null;
        }

        if ($instance instanceof HasLifecycleCallbacksInterface) {
            $instance->onConnect();
        }

        return $this->renderShadowDom($instance);
    }

    /**
     * @param non-empty-string $id
     * @param non-empty-string $method
     * @param array<array-key, mixed> $arguments
     */
    public function notifyInvoke(string $id, string $method, array $arguments): mixed
    {
        $instance = $this->instances[$id] ?? null;

        if (!$instance instanceof HasMethodsInterface) {
            return null;
        }

        return $instance->onMethodCalled($method, $arguments);
    }

    /**
     * @param non-empty-string $id
     */
    public function notifyDisconnect(string $id): void
    {
        $instance = $this->instances[$id] ?? null;

        if (!$instance instanceof HasLifecycleCallbacksInterface) {
            return;
        }

        $instance->onDisconnect();
    }

    /**
     * @param non-empty-string $id
     * @param non-empty-string $name
     */
    public function notifyAttributeChange(string $id, string $name, ?string $value, ?string $previous): void
    {
        $instance = $this->instances[$id] ?? null;

        if (!$instance instanceof HasObservedAttributesInterface) {
            return;
        }

        $instance->onAttributeChanged($name, $value, $previous);
    }

    public function destroyAll(): void
    {
        foreach ($this->instances as $id => $_) {
            $this->notifyDisconnect($id);

            unset($this->instances[$id]);
        }

        $this->instances = [];
    }
}
