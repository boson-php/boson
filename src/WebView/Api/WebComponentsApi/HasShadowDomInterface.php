<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi;

interface HasShadowDomInterface
{
    /**
     * Returns Shadow DOM content.
     */
    public function render(): string;
}
