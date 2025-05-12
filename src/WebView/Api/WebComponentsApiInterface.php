<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

interface WebComponentsApiInterface
{
    /**
     * @param class-string $component
     */
    public function add(string $component): void;
}
