<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi;

abstract class WebComponent implements
    HasObservedAttributesInterface,
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
        return [];
    }

    public function render(): string
    {
        return '<slot />';
    }
}
