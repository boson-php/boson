<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\DisconnectMethod;

interface DisconnectMethodReaderInterface
{
    /**
     * @param class-string $component
     *
     * @return non-empty-string|null
     */
    public function findDisconnectMethod(string $component): ?string;
}
