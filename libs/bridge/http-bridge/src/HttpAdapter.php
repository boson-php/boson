<?php

declare(strict_types=1);

namespace Boson\Bridge\Http;

use Boson\Component\GlobalsProvider\CompoundServerGlobalsProvider;
use Boson\Component\GlobalsProvider\DefaultServerGlobalsProvider;
use Boson\Component\GlobalsProvider\ServerGlobalsProviderInterface;
use Boson\Component\GlobalsProvider\StaticServerGlobalsProvider;
use Boson\Component\Http\Body\BodyDecoderFactory;
use Boson\Component\Http\Body\BodyDecoderInterface;
use Boson\Component\Http\Body\MultipartFormDataDecoder;
use Boson\Component\Http\Body\NativeFormUrlEncodedDecoded;
use Boson\Contracts\Http\RequestInterface;

/**
 * Abstract base class for HTTP adapters that convert between Boson HTTP DTO
 * objects and framework-specific HTTP objects.
 *
 * This class provides common functionality for converting HTTP requests and
 * responses between Boson's internal format and various framework-specific
 * formats (like Symfony, Laravel, or PSR-7).
 *
 * The class is designed to be extended by specific framework adapters, which
 * should implement the conversion logic for their respective framework's HTTP
 * Request/Response objects.
 *
 * @template-covariant TRequest of object
 * @template TResponse of object
 *
 * @template-implements RequestAdapterInterface<TRequest>
 * @template-implements ResponseAdapterInterface<TResponse>
 */
abstract readonly class HttpAdapter implements
    RequestAdapterInterface,
    ResponseAdapterInterface
{
    /**
     * Provider for PHP server globals (`$_SERVER` variable)
     */
    protected ServerGlobalsProviderInterface $server;

    /**
     * Decoder for request body (`$_POST` variable)
     */
    protected BodyDecoderInterface $post;

    public function __construct(
        ?ServerGlobalsProviderInterface $server = null,
        ?BodyDecoderInterface $body = null,
    ) {
        $this->server = $server ?? $this->createDefaultServerGlobalsProvider();
        $this->post = $body ?? $this->createDefaultBodyDecoder();
    }

    /**
     * Decodes the request body into an array.
     *
     * This method uses the configured body decoder to parse the request body
     * into a parsed array of body parameters.
     *
     * @return array<non-empty-string, scalar|array<array-key, mixed>|null>
     */
    protected function getDecodedBody(RequestInterface $request): array
    {
        return $this->post->decode($request);
    }

    /**
     * Gets server parameters from the request.
     *
     * This method uses the configured server globals provider to extract
     * server parameters (headers, etc.) from the request.
     *
     * @return array<non-empty-string, scalar>
     */
    protected function getServerParameters(RequestInterface $request): array
    {
        return $this->server->getServerGlobals($request);
    }

    /**
     * Gets query parameters from the request URL.
     *
     * This method parses the query string from the request URL and returns
     * the parameters as a parsed array.
     *
     * TODO in the future it may be split in a separate package
     *      similar to `boson-php/http-body-decoder`
     *
     * @return array<non-empty-string, string|array<array-key, string>>
     */
    protected function getQueryParameters(RequestInterface $request): array
    {
        $query = \parse_url($request->url, \PHP_URL_QUERY);

        if (!\is_string($query) || $query === '') {
            return [];
        }

        \parse_str($query, $result);

        /** @var array<non-empty-string, string|array<array-key, string>> */
        return $result;
    }

    /**
     * Creates the default server globals provider.
     */
    private function createDefaultServerGlobalsProvider(): ServerGlobalsProviderInterface
    {
        return new CompoundServerGlobalsProvider([
            new StaticServerGlobalsProvider(),
            new DefaultServerGlobalsProvider(),
        ]);
    }

    /**
     * Creates the default body decoder.
     */
    private function createDefaultBodyDecoder(): BodyDecoderInterface
    {
        return new BodyDecoderFactory([
            new NativeFormUrlEncodedDecoded(),
            new MultipartFormDataDecoder(),
        ]);
    }
}
