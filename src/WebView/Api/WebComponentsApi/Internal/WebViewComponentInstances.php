<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Internal;

use Boson\WebView\Api\ScriptsApiInterface;
use Boson\WebView\Api\WebComponentsApi\HasLifecycleCallbacksInterface;
use Boson\WebView\Api\WebComponentsApi\HasObservedAttributesInterface;
use Boson\WebView\Api\WebComponentsApi\HasShadowDomInterface;
use Boson\WebView\Api\WebComponentsApi\Instantiator\WebComponentInstantiatorInterface;
use Boson\WebView\Api\WebComponentsApi\WebComponentContext;

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
        private readonly ScriptsApiInterface $scripts,
        private readonly WebComponentInstantiatorInterface $instantiator,
    ) {}

    /**
     * @param non-empty-string $id
     * @param non-empty-string $name
     * @param class-string $component
     */
    public function create(string $id, string $name, string $component): void
    {
        $context = new WebComponentContext(
            name: $name,
            component: $component,
            attributeChanger: new WebViewComponentAttributeChanger(
                scripts: $this->scripts,
                id: $id,
            ),
        );

        $this->instances[$id] = $this->instantiator->create($context);
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
     *
     * @throws \Throwable
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
