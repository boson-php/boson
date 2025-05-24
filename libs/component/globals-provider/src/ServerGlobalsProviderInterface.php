<?php

declare(strict_types=1);

namespace Boson\Component\GlobalsProvider;

use Boson\Contracts\Http\RequestInterface;

/**
 * Decode and extract PHP `$_SERVER` globals from {@see RequestInterface}.
 */
interface ServerGlobalsProviderInterface
{
    /**
     * @return array<non-empty-string, scalar>
     */
    public function getServerGlobals(RequestInterface $request): array;
}
