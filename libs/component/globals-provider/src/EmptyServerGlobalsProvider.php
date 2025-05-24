<?php

declare(strict_types=1);

namespace Boson\Component\GlobalsProvider;

use Boson\Contracts\Http\RequestInterface;

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
