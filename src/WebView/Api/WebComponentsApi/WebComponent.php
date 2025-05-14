<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi;

abstract class WebComponent implements
    HasClassNameInterface,
    HasObservedAttributesInterface,
    HasMethodsInterface,
    HasLifecycleCallbacksInterface,
    HasShadowDomInterface,
    AttributeChangerInterface
{
    public function __construct(
        /**
         * @var WebComponentContext<$this>
         */
        protected readonly WebComponentContext $context,
    ) {}

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
