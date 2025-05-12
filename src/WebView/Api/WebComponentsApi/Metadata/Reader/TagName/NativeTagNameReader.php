<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\TagName;

final readonly class NativeTagNameReader implements TagNameReaderInterface
{
    public function getTagName(string $component): string
    {
        $name = $this->getShortClassName($component);

        return $this->toTagName($name);
    }

    /**
     * @param non-empty-string $name
     * @return non-empty-lowercase-string
     */
    private function toTagName(string $name): string
    {
        $name = \preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'], '\1-\2', $name);

        return \strtolower($name);
    }

    /**
     * @param class-string $component
     * @return non-empty-string
     */
    private function getShortClassName(string $component): string
    {
        $component = \trim($component, '\\');

        if (($offset = \strpos($component, '\\')) !== false) {
            return \substr($component, $offset + 1);
        }

        return $component;
    }
}
