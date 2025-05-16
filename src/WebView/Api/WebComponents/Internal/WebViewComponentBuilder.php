<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Internal;

use Boson\Application;
use Boson\WebView\Api\WebComponents\Component\HasMethodsInterface;
use Boson\WebView\Api\WebComponents\Component\HasObservedAttributesInterface;
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
    private function hasObservedAttributes(string $component): bool
    {
        return \is_subclass_of($component, HasObservedAttributesInterface::class, true)
            && $component::getObservedAttributeNames() !== [];
    }

    /**
     * @param class-string $component
     */
    private function hasMethods(string $component): bool
    {
        return \is_subclass_of($component, HasMethodsInterface::class, true)
            && $component::getMethodNames() !== [];
    }

    private function hasShadowRoot(string $component): bool
    {
        return \is_subclass_of($component, HasShadowDomInterface::class, true);
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
        $hasObservedAttributes = $this->hasObservedAttributes($component);

        $methodNames = [];

        if ($this->hasMethods($component)) {
            $methodNames = $component::getMethodNames();
        }

        \ob_start();
        require __DIR__ . '/web-component.js.php';

        /** @var non-empty-string */
        return (string) \ob_get_clean();
    }
}
