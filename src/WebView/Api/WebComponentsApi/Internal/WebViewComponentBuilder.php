<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Internal;

use Boson\Application;
use Boson\WebView\Api\WebComponentsApi\HasMethodsInterface;
use Boson\WebView\Api\WebComponentsApi\HasObservedAttributesInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Api\WebComponentsApi\Internal
 */
final readonly class WebViewComponentBuilder
{
    /**
     * @var non-empty-string
     */
    private const string DEFAULT_CLASS_PREFIX = '__BosonWebComponent';

    public function __construct(
        private Application $app,
        /**
         * @var non-empty-string
         */
        private string $classPrefix = self::DEFAULT_CLASS_PREFIX,
    ) {}

    /**
     * @param non-empty-string $component
     *
     * @return non-empty-string
     */
    private function getClassName(string $component): string
    {
        $trimmed = \trim($component, '\\');

        return $this->classPrefix . \str_replace('\\', '_', $trimmed);
    }

    /**
     * @param class-string $component
     */
    private function hasObservedAttributes(string $component): bool
    {
        return \is_subclass_of($component, HasObservedAttributesInterface::class, true);
    }

    /**
     * @param class-string $component
     */
    private function hasMethods(string $component): bool
    {
        return \is_subclass_of($component, HasMethodsInterface::class, true);
    }

    /**
     * @param non-empty-string $tagName
     * @param class-string $component
     *
     * @return non-empty-string
     */
    public function build(string $tagName, string $component): string
    {
        $isDebug = $this->app->isDebug;
        $className = $this->getClassName($component);

        $hasObservedAttributes = $this->hasObservedAttributes($component)
            && $component::getObservedAttributeNames() !== [];

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
