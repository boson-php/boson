<?php

declare(strict_types=1);

namespace Boson\Bridge\Http;

use Boson\Bridge\Http\Server\GlobalsDecoderInterface;
use Boson\Http\RequestInterface;
use Boson\Http\Response;
use Boson\Http\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface as Psr17ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface as Psr7ServerRequestInterface;

/**
 * @template-covariant TRequest of Psr7ServerRequestInterface = Psr7ServerRequestInterface
 *
 * @template TResponse of Psr7ResponseInterface = Psr7ResponseInterface
 * @template-extends HttpAdapter<TRequest, TResponse>
 */
readonly class Psr7HttpAdapter extends HttpAdapter
{
    /**
     * @param GlobalsDecoderInterface<scalar>|null $server
     * @param GlobalsDecoderInterface<mixed>|null $post
     */
    public function __construct(
        private Psr17ServerRequestFactoryInterface $requests,
        ?GlobalsDecoderInterface $server = null,
        ?GlobalsDecoderInterface $post = null,
    ) {
        parent::__construct($server, $post);
    }

    public function createRequest(RequestInterface $request): Psr7ServerRequestInterface
    {
        /** @var Psr7ServerRequestInterface $result */
        $result = $this->requests->createServerRequest(
            $request->method->name,
            $request->url->toString(),
            $this->server->decode($request),
        );

        $result = $result->withQueryParams((array) $request->url->query);

        foreach ($request->headers as $name => $value) {
            $result = $result->withAddedHeader($name, (string) $value);
        }

        if (($parsedBody = $this->post->decode($request)) !== []) {
            $result = $result->withParsedBody($parsedBody);
        }

        /** @var TRequest */
        return $result;
    }

    public function createResponse(object $response): ResponseInterface
    {
        assert($response instanceof Psr7ResponseInterface);

        return new Response(
            body: (string) $response->getBody(),
            headers: $this->psrResponseHeadersToIterable($response->getHeaders()),
            status: $response->getStatusCode(),
        );
    }

    /**
     * @param array<array-key, array<mixed, string>> $headers
     *
     * @return iterable<non-empty-string, string>
     */
    private function psrResponseHeadersToIterable(array $headers): iterable
    {
        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                if (!\is_string($value) || $name === '' || !\is_string($name)) {
                    continue;
                }

                yield $name => $value;
            }
        }
    }
}
