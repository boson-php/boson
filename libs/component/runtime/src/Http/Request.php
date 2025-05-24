<?php

declare(strict_types=1);

namespace Boson\Http;

use Boson\Contracts\Http\Body\BodyProviderInterface;
use Boson\Contracts\Http\Body\MutableBodyProviderInterface;
use Boson\Contracts\Http\Headers\HeadersProviderInterface;
use Boson\Contracts\Http\Headers\MutableHeadersProviderInterface;
use Boson\Contracts\Http\Method\MethodProviderInterface;
use Boson\Contracts\Http\Method\MutableMethodProviderInterface;
use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\Url\MutableUrlProviderInterface;
use Boson\Contracts\Http\Url\UrlProviderInterface;
use Boson\Http\Body\BodyProviderImpl;
use Boson\Http\Headers\HeadersProviderImpl;
use Boson\Http\Method\MethodProviderImpl;
use Boson\Http\Url\UrlProviderImpl;

/**
 * An implementation of immutable request instance.
 *
 * @phpstan-import-type MethodInputType from MutableMethodProviderInterface
 * @phpstan-import-type MethodOutputType from MethodProviderInterface
 * @phpstan-import-type UrlInputType from MutableUrlProviderInterface
 * @phpstan-import-type UrlOutputType from UrlProviderInterface
 * @phpstan-import-type HeadersListInputType from MutableHeadersProviderInterface
 * @phpstan-import-type HeadersListOutputType from HeadersProviderInterface
 * @phpstan-import-type BodyInputType from MutableBodyProviderInterface
 * @phpstan-import-type BodyOutputType from BodyProviderInterface
 */
final readonly class Request implements RequestInterface
{
    use MethodProviderImpl;
    use UrlProviderImpl;
    use HeadersProviderImpl;
    use BodyProviderImpl;

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
        $this->method = self::castMethod($method);
        $this->url = self::castUrl($url);
        $this->headers = self::castHeaders($headers);
        $this->body = self::castBody($body);
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
        /**
         * @link https://wiki.php.net/rfc/readonly_amendments
         *
         * @phpstan-ignore-next-line : PHPStan does not support PHP 8.3 clone feature
         */
        $this->headers = clone $this->headers;
    }
}
