<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Metadata\Reader\ConnectMethod;

final readonly class NativeConnectMethodReader implements ConnectMethodReaderInterface
{
    public function findConnectMethod(string $component): ?string
    {
        return null;
    }
}
