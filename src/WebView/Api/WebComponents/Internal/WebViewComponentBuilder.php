<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Internal;

use Boson\Application;
use Boson\WebView\Api\WebComponents\Component\HasAttributesInterface;
use Boson\WebView\Api\WebComponents\Component\HasEventListenersInterface;
use Boson\WebView\Api\WebComponents\Component\HasMethodsInterface;
use Boson\WebView\Api\WebComponents\Component\HasPropertiesInterface;
use Boson\WebView\Api\WebComponents\Component\HasShadowDomInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Api\WebComponentsApi\Internal
 */
final readonly class WebViewComponentBuilder
{
    public function __construct(
        private Application $app,
    ) {}

    /**
     * @param class-string $component
     */
    private function hasAttributes(string $component): bool
    {
        return \is_subclass_of($component, HasAttributesInterface::class, true);
    }

    /**
     * @param class-string $component
     */
    private function hasProperties(string $component): bool
    {
        return \is_subclass_of($component, HasPropertiesInterface::class, true);
    }

    /**
     * @param class-string $component
     */
    private function hasMethods(string $component): bool
    {
        return \is_subclass_of($component, HasMethodsInterface::class, true);
    }

    private function hasShadowRoot(string $component): bool
    {
        return \is_subclass_of($component, HasShadowDomInterface::class, true);
    }

    /**
     * @param class-string $component
     */
    private function hasEventListeners(string $component): bool
    {
        return \is_subclass_of($component, HasEventListenersInterface::class, true);
    }

    /**
     * @param array<array-key, mixed> $arguments
     *
     * @return non-empty-string
     */
    private function buildEventListenerArguments(array $arguments): string
    {
        $result = [];

        foreach ($arguments as $name => $argument) {
            $value = match (true) {
                \is_string($argument) => 'e.' . $argument,
                \is_array($argument) => $this->buildEventListenerArguments($argument),
                default => '{}',
            };

            if (\is_int($name)) {
                if (!\is_string($argument)) {
                    continue;
                }

                $name = $argument;
            }

            $result[] = \sprintf('"%s":%s', \addcslashes($name, '"'), $value);
        }

        return '{' . \implode(',', $result) . '}';
    }

    /**
     * @param class-string $component
     *
     * @return list<non-empty-string>
     */
    private function buildAttributes(string $component): array
    {
        if ($this->hasAttributes($component)) {
            /** @var list<non-empty-string> */
            return $component::getAttributeNames();
        }

        return [];
    }

    /**
     * @param class-string $component
     *
     * @return list<non-empty-string>
     */
    private function buildProperties(string $component): array
    {
        if ($this->hasProperties($component)) {
            /** @var list<non-empty-string> */
            return $component::getPropertyNames();
        }

        return [];
    }

    /**
     * @param class-string $component
     *
     * @return list<non-empty-string>
     */
    private function buildMethods(string $component): array
    {
        if ($this->hasMethods($component)) {
            /** @var list<non-empty-string> */
            return $component::getMethodNames();
        }

        return [];
    }

    /**
     * @param class-string $component
     *
     * @return array<non-empty-string, non-empty-string>
     */
    private function buildEventListeners(string $component): array
    {
        $result = [];

        if ($this->hasEventListeners($component)) {
            /** @var array<non-empty-string, array<array-key, mixed>> $eventListeners */
            $eventListeners = $component::getEventListeners();

            foreach ($eventListeners as $eventName => $eventArguments) {
                $result[$eventName] = $this->buildEventListenerArguments(
                    arguments: $eventArguments,
                );
            }
        }

        return $result;
    }

    /**
     * @param non-empty-string $tagName
     * @param non-empty-string $className
     * @param class-string $component
     *
     * @return non-empty-string
     */
    public function build(string $tagName, string $className, string $component): string
    {
        $isDebug = $this->app->isDebug;

        $hasShadowRoot = $this->hasShadowRoot($component);
        $attributeNames = $this->buildAttributes($component);
        $propertyNames = $this->buildProperties($component);
        $methodNames = $this->buildMethods($component);
        $eventListeners = $this->buildEventListeners($component);

        \ob_start();
        require __DIR__ . '/web-component.js.php';

        /** @var non-empty-string */
        return (string) \ob_get_clean();
    }
}
