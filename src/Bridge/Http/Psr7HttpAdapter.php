<?php

declare(strict_types=1);

namespace Boson\Bridge\Http;

use Boson\Bridge\Http\Body\BodyDecoderInterface;
use Boson\Bridge\Http\Server\ServerGlobalsProviderInterface;
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
    public function __construct(
        private Psr17ServerRequestFactoryInterface $requests,
        ?ServerGlobalsProviderInterface $server = null,
        ?BodyDecoderInterface $body = null,
    ) {
        parent::__construct($server, $body);
    }

    public function createRequest(RequestInterface $request): Psr7ServerRequestInterface
    {
        /** @var Psr7ServerRequestInterface $result */
        $result = $this->requests->createServerRequest(
            $request->method,
            $request->url,
            $this->getServerParameters($request),
        );

        // Extend headers list
        foreach ($request->headers as $name => $value) {
            $result = $result->withAddedHeader($name, $value);
        }

        // Extend query params
        $result = $result->withQueryParams(
            query: $this->getQueryParameters($request),
        );

        // Extend body parameters
        $result = $result->withParsedBody(
            data: $this->getDecodedBody($request),
        );

        /** @var TRequest */
        return $result;
    }

    public function createResponse(object $response): ResponseInterface
    {
        assert($response instanceof Psr7ResponseInterface);

        return new Response(
            body: (string) $response->getBody(),
            /** @phpstan-ignore-next-line : Allow PSR headers */
            headers: $response->getHeaders(),
            status: $response->getStatusCode(),
        );
    }
}
