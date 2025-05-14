<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi;

interface HasClassNameInterface
{
    /**
     * @return non-empty-string
     */
    public static function getClassName(): string;
}
