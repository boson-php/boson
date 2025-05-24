<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Component;

interface HasTemplateInterface
{
    /**
     * Returns HTML content string.
     */
    public function render(): string;
}
