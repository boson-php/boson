<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents;

interface HasTemplateInterface
{
    /**
     * Returns HTML content string.
     */
    public function render(): string;
}
