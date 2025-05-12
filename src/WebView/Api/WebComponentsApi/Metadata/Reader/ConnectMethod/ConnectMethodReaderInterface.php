<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\ConnectMethod;

interface ConnectMethodReaderInterface
{
    /**
     * @param class-string $component
     *
     * @return non-empty-string|null
     */
    public function findConnectMethod(string $component): ?string;
}
