<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Component;

/**
 * @phpstan-require-implements HasClassNameInterface
 */
trait HasClassName
{
    public static function getClassName(): string
    {
        $name = \str_replace('\\', '_', static::class);

        return 'BosonWebComponent$' . $name;
    }
}
