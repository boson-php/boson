<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi;

abstract class WebComponent implements
    HasObservedAttributesInterface,
    HasMethodsInterface,
    HasLifecycleCallbacksInterface,
    HasShadowDomInterface,
    AttributeChangerInterface
{
    protected readonly \ReflectionObject $reflection;

    public function __construct(
        /**
         * @var WebComponentContext<$this>
         */
        protected readonly WebComponentContext $context,
    ) {
        $this->reflection = new \ReflectionObject($this);
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
        // Can be overridden
    }

    public function changeAttribute(string $name, ?string $value): void
    {
        $this->context->attributeChanger->changeAttribute($name, $value);
    }

    public static function getObservedAttributeNames(): array
    {
        // Can be overridden
        return [];
    }

    public function onMethodCalled(string $method, array $args = []): mixed
    {
        // Can be overridden
        return null;
    }

    public static function getMethodNames(): array
    {
        // Can be overridden
        return [];
    }

    public function render(): string
    {
        // Can be overridden
        return '<slot />';
    }
}
