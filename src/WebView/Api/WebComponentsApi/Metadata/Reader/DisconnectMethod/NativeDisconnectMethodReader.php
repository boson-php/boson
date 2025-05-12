<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\DisconnectMethod;

final readonly class NativeDisconnectMethodReader implements DisconnectMethodReaderInterface
{
    public function findDisconnectMethod(string $component): ?string
    {
        return null;
    }
}
