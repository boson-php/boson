<?php

declare(strict_types=1);

namespace Boson\Component\Http;

use Boson\Component\Http\Body\MutableBodyProviderImpl;
use Boson\Component\Http\Headers\MutableHeadersProviderImpl;
use Boson\Component\Http\Method\MutableMethodProviderImpl;
use Boson\Component\Http\Url\MutableUrlProviderImpl;
use Boson\Contracts\Http\Body\MutableBodyProviderInterface;
use Boson\Contracts\Http\Headers\MutableHeadersProviderInterface;
use Boson\Contracts\Http\Method\MutableMethodProviderInterface;
use Boson\Contracts\Http\MutableRequestInterface;
use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\Url\MutableUrlProviderInterface;

/**
 * @phpstan-import-type MethodInputType from MutableMethodProviderInterface
 * @phpstan-import-type UrlInputType from MutableUrlProviderInterface
 * @phpstan-import-type BodyInputType from MutableBodyProviderInterface
 * @phpstan-import-type HeadersListInputType from MutableHeadersProviderInterface
 * @phpstan-import-type MutableMethodOutputType from MutableMethodProviderInterface
 * @phpstan-import-type MutableUrlOutputType from MutableUrlProviderInterface
 */
class MutableRequest implements MutableRequestInterface
{
    use MutableMethodProviderImpl;
    use MutableUrlProviderImpl;
    use MutableHeadersProviderImpl;
    use MutableBodyProviderImpl;

    /**
     * @param MethodInputType $method
     * @param UrlInputType $url
     * @param HeadersListInputType $headers
     * @param BodyInputType $body
     */
    public function __construct(
        string|\Stringable $method = MutableMethodProviderInterface::DEFAULT_METHOD,
        string|\Stringable $url = MutableUrlProviderInterface::DEFAULT_URL,
        iterable $headers = MutableHeadersProviderInterface::DEFAULT_HEADERS,
        string|\Stringable $body = MutableBodyProviderInterface::DEFAULT_BODY,
    ) {
        $this->method = $method;
        $this->url = $url;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Creates new request instance from another one.
     *
     * @api
     */
    public static function createFromRequest(RequestInterface $request): self
    {
        if ($request instanceof self) {
            return clone $request;
        }

        return new self(
            method: $request->method,
            url: $request->url,
            headers: $request->headers,
            body: $request->body,
        );
    }

    public function __clone(): void
    {
        $this->headers = clone $this->headers;
    }
}
