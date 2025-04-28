<?php

declare(strict_types=1);

namespace Boson\Bridge\Http;

use Boson\Http\RequestInterface;
use Boson\Http\Response;
use Boson\Http\ResponseInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
            uri: (string) $request->url,
            method: $request->method->name,
            parameters: (array) $request->url->query,
            server: $this->server->decode($request),
            content: $request->body,
        );

        foreach ($this->post->decode($request) as $parameter => $value) {
            /** @phpstan-ignore-next-line : Allow to set any values */
            $symfonyRequest->request->set($parameter, $value);
        }

        return $symfonyRequest;
    }

    public function createResponse(object $response): ResponseInterface
    {
        assert($response instanceof SymfonyResponse);

        return new Response(
            body: (string) $response->getContent(),
            headers: $this->headerBagToIterable($response->headers),
            status: $response->getStatusCode(),
        );
    }

    /**
     * @return iterable<non-empty-string, string>
     */
    private function headerBagToIterable(ResponseHeaderBag $headers): iterable
    {
        foreach ($headers->all() as $name => $values) {
            foreach ($values as $value) {
                if (!\is_string($value) || $name === '') {
                    continue;
                }

                yield $name => $value;
            }
        }
    }
}
