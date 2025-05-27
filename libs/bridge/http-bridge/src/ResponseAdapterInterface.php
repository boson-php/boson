<?php

declare(strict_types=1);

namespace Boson\Bridge\Http;

use Boson\Contracts\Http\ResponseInterface;

/**
 * Interface for adapters that convert framework-specific responses to Boson
 * HTTP {@see ResponseInterface} responses.
 *
 * Defines the contract for adapters that convert between framework-specific
 * response objects and Boson's internal HTTP response format. It is used by
 * the HTTP bridge to integrate with different frameworks like Symfony, Laravel,
 * or PSR-7.
 *
 * @template TResponse of object
 */
interface ResponseAdapterInterface
{
    /**
     * Creates a new Boson response instance from a framework-specific response.
     *
     * ```
     * $bosonResponse = new SymfonyResponseAdapter()
     *     ->createResponse($symfonyResponse);
     * ```
     *
     * @param TResponse $response The framework-specific response to convert
     */
    public function createResponse(object $response): ResponseInterface;
}
