<?php

declare(strict_types=1);

namespace Boson\Bridge\Psr\Http;

use Boson\Bridge\Http\HttpAdapter;
use Boson\Component\GlobalsProvider\ServerGlobalsProviderInterface;
use Boson\Component\Http\Body\BodyDecoderInterface;
use Boson\Component\Http\Response;
use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface as Psr17ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface as Psr7ServerRequestInterface;

/**
 * @template-covariant TRequest of Psr7ServerRequestInterface = Psr7ServerRequestInterface
 * @template TResponse of Psr7ResponseInterface = Psr7ResponseInterface
 *
 * @template-extends HttpAdapter<TRequest, TResponse>
 */
readonly class Psr7HttpAdapter extends HttpAdapter
{
    public function __construct(
        private Psr17ServerRequestFactoryInterface $requestFactory,
        ?ServerGlobalsProviderInterface $server = null,
        ?BodyDecoderInterface $body = null,
    ) {
        parent::__construct($server, $body);
    }

    /**
     * @return TRequest
     */
    private function createServerRequest(RequestInterface $request): Psr7ServerRequestInterface
    {
        /** @var TRequest */
        return $this->requestFactory->createServerRequest(
            $request->method,
            $request->url,
            $this->getServerParameters($request),
        );
    }

    private function extendHeaders(Psr7ServerRequestInterface $psr7, RequestInterface $request): Psr7ServerRequestInterface
    {
        foreach ($request->headers as $name => $value) {
            $modified = $psr7->withAddedHeader($name, $value);

            // PSR-7 contains an architectural problem that does not
            // guarantee the return of the same type.
            if ($modified instanceof Psr7ServerRequestInterface) {
                $psr7 = $modified;
            }
        }

        return $psr7;
    }

    private function extendQueryParams(Psr7ServerRequestInterface $psr7, RequestInterface $request): Psr7ServerRequestInterface
    {
        return $psr7->withQueryParams(
            query: $this->getQueryParameters($request),
        );
    }

    private function extendParsedBody(Psr7ServerRequestInterface $psr7, RequestInterface $request): Psr7ServerRequestInterface
    {
        return $psr7->withParsedBody(
            data: $this->getDecodedBody($request),
        );
    }

    public function createRequest(RequestInterface $request): Psr7ServerRequestInterface
    {
        $psr7 = $this->createServerRequest($request);

        $psr7 = $this->extendHeaders($psr7, $request);
        $psr7 = $this->extendQueryParams($psr7, $request);
        $psr7 = $this->extendParsedBody($psr7, $request);

        /** @var TRequest */
        return $psr7;
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
