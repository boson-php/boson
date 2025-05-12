<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi;

use Boson\Shared\PropertyAccessor\PropertyAccessorInterface;
use Boson\WebView\Api\WebComponentsApi\Instantiator\WebComponentInstantiatorInterface;
use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentHtmlTemplateMetadata;
use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentMetadata;
use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentMethodTemplateMetadata;
use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentPropertyTemplateMetadata;
use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentTemplateMetadata;

/**
 * Provides components instances
 *
 * @internal this is an internal library class, please do not use it in your code.
 * @psalm-internal Boson\WebView\Api
 */
final class WebViewComponentInstances
{
    /**
     * A map containing a link between a client instance ID and PHP component instance.
     *
     * @var array<non-empty-string, ComponentInstance<object>>
     */
    private array $instances = [];

    public function __construct(
        private readonly WebComponentInstantiatorInterface $instantiator,
        private readonly PropertyAccessorInterface $accessor,
    ) {}

    /**
     * @param non-empty-string $id
     */
    public function create(string $id, WebComponentMetadata $meta): void
    {
        $this->instances[$id] = new ComponentInstance(
            instance: $this->instantiator->create($meta),
            meta: $meta,
        );
    }

    public function notifyConnect(string $id): ?string
    {
        $instance = $this->instances[$id] ?? null;

        if ($instance === null) {
            return null;
        }

        if ($instance->meta->onConnect !== null) {
            $instance->instance->{$instance->meta->onConnect}();
        }

        return $this->renderTemplate(
            context: $instance->instance,
            meta: $instance->meta->template,
        );
    }

    private function renderTemplate(object $context, ?WebComponentTemplateMetadata $meta): ?string
    {
        return match (true) {
            $meta instanceof WebComponentHtmlTemplateMetadata
                => $this->renderHtmlTemplate($meta),
            $meta instanceof WebComponentMethodTemplateMetadata
                => $this->renderMethodTemplate($context, $meta),
            $meta instanceof WebComponentPropertyTemplateMetadata
                => $this->renderPropertyTemplate($context, $meta),
            default => null,
        };
    }

    private function renderHtmlTemplate(WebComponentHtmlTemplateMetadata $meta): string
    {
        return $meta->html;
    }

    private function renderPropertyTemplate(object $context, WebComponentPropertyTemplateMetadata $meta): string
    {
        return $context->{$meta->name};
    }

    private function renderMethodTemplate(object $context, WebComponentMethodTemplateMetadata $meta): string
    {
        return $context->{$meta->name}();
    }

    public function notifyDisconnect(string $id): void
    {
        $instance = $this->instances[$id] ?? null;

        if ($instance === null) {
            return;
        }

        if ($instance->meta->onDisconnect !== null) {
            $instance->instance->{$instance->meta->onDisconnect}();
        }
    }

    public function notifyAttributeChange(string $id, string $name, ?string $value, ?string $previous): void
    {
        $instance = $this->instances[$id] ?? null;
        $property = $instance?->meta->findPropertyByAttributeName($name);

        // Check that instance is present
        // And such attribute is observable
        if ($property === null) {
            return;
        }

        // Check that such attribute is writable
        if (!$this->accessor->isWritable($instance->instance, $property)) {
            return;
        }

        $this->accessor->setValue($instance->instance, $property, $value);
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
