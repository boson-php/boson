<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Internal;

use Boson\WebView\Api\DataApiInterface;
use Boson\WebView\Api\ScriptsApiInterface;
use Boson\WebView\Api\WebComponentsApi\HasLifecycleCallbacksInterface;
use Boson\WebView\Api\WebComponentsApi\HasMethodsInterface;
use Boson\WebView\Api\WebComponentsApi\HasObservedAttributesInterface;
use Boson\WebView\Api\WebComponentsApi\HasShadowDomInterface;
use Boson\WebView\Api\WebComponentsApi\HasTemplateInterface;
use Boson\WebView\Api\WebComponentsApi\Instantiator\WebComponentInstantiatorInterface;
use Boson\WebView\Api\WebComponentsApi\ReactiveElementContext;

/**
 * Provides components instances
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Api
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
        private readonly DataApiInterface $data,
        private readonly ScriptsApiInterface $scripts,
        private readonly WebComponentInstantiatorInterface $instantiator,
    ) {}

    /**
     * @param non-empty-string $id
     * @param non-empty-string $name
     * @param class-string $component
     */
    public function create(string $id, string $name, string $component): ?string
    {
        $hasShadowDom = \is_subclass_of($component, HasShadowDomInterface::class, true);

        $interactor = new ElementInteractor(
            id: $id,
            data: $this->data,
            scripts: $this->scripts,
        );

        $context = new ReactiveElementContext(
            name: $name,
            component: $component,
            attributes: new ReactiveAttributeMap($interactor),
            content: $hasShadowDom
                ? new ReactiveShadowDomContainer($interactor)
                : new ReactiveTemplateContainer($interactor),
        );

        $this->instances[$id] = $instance = $this->instantiator->create($context);

        if ($hasShadowDom === false && $instance instanceof HasTemplateInterface) {
            return $instance->render();
        }

        return null;
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

        if ($instance instanceof HasShadowDomInterface) {
            return $instance->render();
        }

        return null;
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
