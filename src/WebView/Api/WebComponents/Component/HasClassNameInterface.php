<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Component;

interface HasClassNameInterface
{
    /**
     * @return non-empty-string
     */
    public static function getClassName(): string;
}
