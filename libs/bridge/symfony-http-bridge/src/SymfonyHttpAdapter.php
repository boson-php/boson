<?php

declare(strict_types=1);

namespace Boson\Bridge\Symfony\Http;

use Boson\Bridge\Http\HttpAdapter;
use Boson\Component\Http\Response;
use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\ResponseInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Adapter for converting between Boson HTTP objects and Symfony HTTP objects.
 *
 * This class provides the implementation for converting between Boson's
 * internal HTTP format and Symfony's HTTP objects.
 *
 * It handles the conversion of:
 * - HTTP requests: Boson {@see RequestInterface} → Symfony {@see SymfonyRequest}
 * - HTTP responses: Symfony {@see SymfonyResponse} → Boson {@see ResponseInterface}
 *
 * @template-covariant TRequest of SymfonyRequest = SymfonyRequest
 * @template TResponse of SymfonyResponse = SymfonyResponse
 *
 * @template-extends HttpAdapter<SymfonyRequest, SymfonyResponse>
 */
readonly class SymfonyHttpAdapter extends HttpAdapter
{
    /**
     * Creates a new Symfony {@see SymfonyRequest} instance from
     * a Boson {@see RequestInterface}.
     *
     * @return TRequest
     */
    public function createRequest(RequestInterface $request): SymfonyRequest
    {
        $symfony = SymfonyRequest::create(
            uri: $request->url,
            method: $request->method,
            parameters: $this->getQueryParameters($request),
            server: $this->getServerParameters($request),
            content: $request->body,
        );

        $symfony->request = new InputBag(
            parameters: $this->getDecodedBody($request),
        );

        /** @var TRequest */
        return $symfony;
    }

    /**
     * Creates a new Boson {@see ResponseInterface} instance from a
     * Symfony {@see SymfonyResponse}.
     *
     * @param TResponse $response
     */
    public function createResponse(object $response): ResponseInterface
    {
        assert($response instanceof SymfonyResponse);

        return new Response(
            body: (string) $response->getContent(),
            /** @phpstan-ignore-next-line : Allow Symfony headers */
            headers: $response->headers->all(),
            status: $response->getStatusCode(),
        );
    }
}
