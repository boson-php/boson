<?php

declare(strict_types=1);

namespace Boson\Bridge\Http;

use Boson\Component\Http\Response;
use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\ResponseInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @template-covariant TRequest of SymfonyRequest = SymfonyRequest
 *
 * @template TResponse of SymfonyResponse = SymfonyResponse
 * @template-extends HttpAdapter<SymfonyRequest, SymfonyResponse>
 */
readonly class SymfonyHttpAdapter extends HttpAdapter
{
    public function createRequest(RequestInterface $request): SymfonyRequest
    {
        $symfonyRequest = SymfonyRequest::create(
            uri: $request->url,
            method: $request->method,
            parameters: $this->getQueryParameters($request),
            server: $this->getServerParameters($request),
            content: $request->body,
        );

        $symfonyRequest->request = new InputBag(
            parameters: $this->getDecodedBody($request),
        );

        return $symfonyRequest;
    }

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
