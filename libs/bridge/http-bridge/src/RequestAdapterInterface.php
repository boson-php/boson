<?php

declare(strict_types=1);

namespace Boson\Bridge\Http;

use Boson\Contracts\Http\RequestInterface;

/**
 * Interface for adapters that convert Boson HTTP {@see RequestInterface}
 * requests to framework-specific requests.
 *
 * Defines the contract for adapters that convert between Boson's internal HTTP
 * request format and framework-specific request objects. It is used by the
 * HTTP bridge to integrate with different frameworks like Symfony, Laravel,
 * or PSR-7.
 *
 * @template-covariant TRequest of object
 */
interface RequestAdapterInterface
{
    /**
     * Creates a new framework-specific request instance from a Boson request.
     *
     * ```
     * $symfonyRequest = new SymfonyRequestAdapter()
     *     ->createRequest($bosonRequest);
     * ```
     *
     * @return TRequest A new framework-specific request instance
     */
    public function createRequest(RequestInterface $request): object;
}
