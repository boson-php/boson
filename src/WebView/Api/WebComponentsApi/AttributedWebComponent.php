<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi;

use Boson\WebView\Api\WebComponentsApi\Attribute\AsAttribute;
use Boson\WebView\Api\WebComponentsApi\Attribute\AsMethod;
use Boson\WebView\Api\WebComponentsApi\Attribute\AsTemplate;
use Boson\WebView\Api\WebComponentsApi\Attribute\AsWebComponent;
use Boson\WebView\Api\WebComponentsApi\AttributedWebComponent\MethodMetadata;
use Boson\WebView\Api\WebComponentsApi\AttributedWebComponent\AttributeMetadata;

abstract class AttributedWebComponent extends WebComponent
{
    /**
     * @var \Closure():string|null
     */
    private ?\Closure $render = null;

    /**
     * @var array<non-empty-string, MethodMetadata>
     */
    private static array $registeredComponentMethods = [];

    /**
     * @var array<non-empty-string, AttributeMetadata>
     */
    private static array $registeredComponentAttributes = [];

    private static function getAsWebComponentAttribute(): ?AsWebComponent
    {
        $reflection = new \ReflectionClass(static::class);

        foreach ($reflection->getAttributes(AsWebComponent::class) as $attribute) {
            return $attribute->newInstance();
        }

        return null;
    }

    public static function getClassName(): string
    {
        $asWebComponent = self::getAsWebComponentAttribute();

        // Try to fetch class name from attribute
        if ($asWebComponent?->className !== null) {
            return $asWebComponent->className;
        }

        return parent::getClassName();
    }

    public function onMethodCalled(string $method, array $args = []): mixed
    {
        $info = self::$registeredComponentMethods[$method] ?? null;

        if ($info === null) {
            throw new \BadMethodCallException(\sprintf(
                'The "%s" method of <%s /> is not defined',
                $method,
                $this->tagName,
            ));
        }

        try {
            return $info->method->invokeArgs($this, $args);
        } finally {
            if ($info->renderAfterCall) {
                $this->refresh();
            }
        }
    }

    public static function getMethodNames(): array
    {
        if (self::$registeredComponentMethods !== []) {
            return \array_keys(self::$registeredComponentMethods);
        }

        $reflection = new \ReflectionClass(static::class);

        // Try to fetch methods from AsClientMethod
        foreach ($reflection->getMethods() as $method) {
            if ($method->name === '') {
                continue;
            }

            foreach ($method->getAttributes(AsMethod::class) as $attribute) {
                /** @var AsMethod $instance */
                $instance = $attribute->newInstance();

                self::$registeredComponentMethods[$instance->name ?? $method->name] = new MethodMetadata(
                    method: $method,
                    renderAfterCall: $instance->renderAfterCall,
                );
            }
        }

        return \array_keys(self::$registeredComponentMethods);
    }

    public function onAttributeChanged(string $attribute, ?string $value, ?string $previous): void
    {
        $info = self::$registeredComponentAttributes[$attribute] ?? null;

        if ($info === null) {
            throw new \BadMethodCallException(\sprintf(
                'The "%s" attribute of <%s /> is not observable',
                $attribute,
                $this->tagName,
            ));
        }

        try {
            if ($info->enableHooks) {
                $info->property->setValue($this, $value);
            } else {
                $info->property->setRawValue($this, $value);
            }
        } finally {
            if ($info->renderAfterCall) {
                $this->refresh();
            }
        }
    }

    public static function getObservedAttributeNames(): array
    {
        if (self::$registeredComponentAttributes !== []) {
            return \array_keys(self::$registeredComponentAttributes);
        }

        $reflection = new \ReflectionClass(static::class);

        // Try to fetch methods from AsClientMethod
        foreach ($reflection->getProperties() as $property) {
            if ($property->name === '' || $property->isStatic()) {
                continue;
            }

            foreach ($property->getAttributes(AsAttribute::class) as $attribute) {
                /** @var AsAttribute $instance */
                $instance = $attribute->newInstance();

                self::$registeredComponentAttributes[$instance->name ?? $property->name] = new AttributeMetadata(
                    property: $property,
                    enableHooks: $instance->enableHooks,
                    renderAfterCall: $instance->renderAfterCall,
                );
            }
        }

        return \array_keys(self::$registeredComponentAttributes);
    }

    /**
     * @return \Closure():string
     */
    private function getRenderCallback(): \Closure
    {
        $asWebComponent = self::getAsWebComponentAttribute();

        // Try to fetch template from attribute
        if ($asWebComponent?->template !== null) {
            return static fn(): string => $asWebComponent->template;
        }

        $reflection = new \ReflectionClass(static::class);

        // Try to fetch template from AsTemplate on property
        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes(AsTemplate::class) as $_) {
                /** @phpstan-ignore-next-line Known casting error */
                return fn(): string => (string) $property->getValue($this);
            }
        }

        // Try to fetch template from AsTemplate on method
        foreach ($reflection->getMethods() as $method) {
            foreach ($method->getAttributes(AsTemplate::class) as $_) {
                /** @phpstan-ignore-next-line Known casting error */
                return fn(): string => (string) $method->invoke($this);
            }
        }

        return fn(): string => parent::render();
    }

    public function render(): string
    {
        return ($this->render ??= $this->getRenderCallback())();
    }
}
