<?php

declare(strict_types=1);

namespace Boson\Bridge\Http;

use Boson\Bridge\Http\Body\BodyDecoderFactory;
use Boson\Bridge\Http\Body\BodyDecoderInterface;
use Boson\Bridge\Http\Body\MultipartFormDataDecoder;
use Boson\Bridge\Http\Body\NativeFormUrlEncodedDecoded;
use Boson\Bridge\Http\Server\DefaultServerGlobalsProvider;
use Boson\Bridge\Http\Server\ServerGlobalsProviderInterface;
use Boson\Bridge\Http\Server\StaticServerGlobalsProvider;
use Boson\Http\RequestInterface;

/**
 * @template-covariant TRequest of object
 *
 * @template TResponse of object
 * @template-implements RequestAdapterInterface<TRequest>
 * @template-implements ResponseAdapterInterface<TResponse>
 */
abstract readonly class HttpAdapter implements
    RequestAdapterInterface,
    ResponseAdapterInterface
{
    protected ServerGlobalsProviderInterface $server;
    protected BodyDecoderInterface $post;

    public function __construct(
        ?ServerGlobalsProviderInterface $server = null,
        ?BodyDecoderInterface $body = null,
    ) {
        $this->server = $server ?? $this->createServerGlobalsDecoder();
        $this->post = $body ?? $this->createPostGlobalsDecoder();
    }

    /**
     * @return array<non-empty-string, scalar|array<array-key, mixed>|null>
     */
    protected function getDecodedBody(RequestInterface $request): array
    {
        return $this->post->decode($request);
    }

    /**
     * @return array<non-empty-string, scalar>
     */
    protected function getServerParameters(RequestInterface $request): array
    {
        return $this->server->getServerGlobals($request);
    }

    /**
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

    protected function createServerGlobalsDecoder(): ServerGlobalsProviderInterface
    {
        return new DefaultServerGlobalsProvider(
            delegate: new StaticServerGlobalsProvider(),
        );
    }

    protected function createPostGlobalsDecoder(): BodyDecoderInterface
    {
        return new BodyDecoderFactory([
            new NativeFormUrlEncodedDecoded(),
            new MultipartFormDataDecoder(),
        ]);
    }
}
