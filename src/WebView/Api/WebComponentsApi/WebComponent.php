<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi;

use JetBrains\PhpStorm\Language;

abstract class WebComponent implements
    HasClassNameInterface,
    HasObservedAttributesInterface,
    HasMethodsInterface,
    HasLifecycleCallbacksInterface,
    HasTemplateInterface,
    AttributeChangerInterface,
    TemplateChangerInterface
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
        $this->changeTemplate($this->render());
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
        $this->changeTemplate($this->render());

        return null;
    }

    public static function getMethodNames(): array
    {
        // Can be overridden
        return [];
    }

    public function render(): string
    {
        if ($this instanceof \Stringable) {
            return (string) $this;
        }

        // Can be overridden
        return '<slot />';
    }

    public function changeTemplate(#[Language('HTML')] string $html): void
    {
        $this->context->templateChanger->changeTemplate($html);
    }
}
