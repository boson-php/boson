<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi;

interface AttributeChangerInterface
{
    /**
     * @param non-empty-string $name
     */
    public function changeAttribute(string $name, ?string $value): void;
}
