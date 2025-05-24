<?php

declare(strict_types=1);

namespace Boson\Bridge\Http\Server;

use Boson\Http\RequestInterface;

/**
 * Returns empty parameters.
 */
final readonly class EmptyServerGlobalsProvider implements ServerGlobalsProviderInterface
{
    public function getServerGlobals(RequestInterface $request): array
    {
        return [];
    }
}
