<?php

declare(strict_types=1);

namespace Boson\Bridge\Http;

use Boson\Http\RequestInterface;

/**
 * @template-covariant TRequest of object
 */
interface RequestAdapterInterface
{
    /**
     * Creates new internal (framework-aware) request instance from
     * Boson request argument.
     *
     * @return TRequest
     */
    public function createRequest(RequestInterface $request): object;
}
