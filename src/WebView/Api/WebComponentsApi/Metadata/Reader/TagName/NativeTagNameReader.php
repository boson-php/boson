<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\TagName;

final readonly class NativeTagNameReader implements TagNameReaderInterface
{
    public function getTagName(string $component): string
    {
        $name = $this->getShortClassName($component);

        /** @var non-empty-string $name */
        $name = \preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'], '\1-\2', $name);

        /** @var non-empty-lowercase-string */
        return \strtolower($name);
    }

    /**
     * @param class-string $component
     *
     * @return non-empty-string
     */
    private function getShortClassName(string $component): string
    {
        $component = \trim($component, '\\');

        if (($offset = \strpos($component, '\\')) !== false) {
            /** @var non-empty-string */
            return \substr($component, $offset + 1);
        }

        /** @var non-empty-string */
        return $component;
    }
}
