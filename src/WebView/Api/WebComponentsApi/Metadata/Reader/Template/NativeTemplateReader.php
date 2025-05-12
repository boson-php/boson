<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\Template;

use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentMethodTemplateMetadata;
use Boson\WebView\Api\WebComponentsApi\Metadata\WebComponentTemplateMetadata;

final class NativeTemplateReader implements TemplateReaderInterface
{
    /**
     * @var non-empty-array<class-string, non-empty-string>
     */
    private const array INTERFACE_TO_METHOD_MAPPING = [
        \Stringable::class => '__toString',
    ];


    public function findTemplate(string $component): ?WebComponentTemplateMetadata
    {
        foreach (self::INTERFACE_TO_METHOD_MAPPING as $interface => $method) {
            if (\is_subclass_of($component, $interface, true)) {
                return new WebComponentMethodTemplateMetadata($method);
            }
        }

        return null;
    }

    /**
     * @param class-string $component
     */
    private function isStringable(string $component): bool
    {
        return \is_subclass_of($component, \Stringable::class, true);
    }
}
