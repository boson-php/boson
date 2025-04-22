<?php

declare(strict_types=1);

namespace Boson\Http;

interface EvolvableRequestInterface extends RequestInterface
{
    /**
     * Return an instance with the provided HTTP method.
     *
     * @phpstan-pure
     *
     * @param MethodInterface|non-empty-string $method
     */
    public function withMethod(MethodInterface|string $method): self;

    /**
     * Returns an instance with the provided URI.
     *
     * @phpstan-pure
     *
     * @param UriInterface|non-empty-string $uri
     */
    public function withUri(UriInterface|string $uri): self;

    /**
     * Returns an instance with the provided headers list.
     *
     * @phpstan-pure
     *
     * @param HeadersInterface|iterable<non-empty-string, string> $headers
     */
    public function withHeaders(HeadersInterface|iterable $headers): self;

    /**
     * Returns an instance with the provided body.
     *
     * @phpstan-pure
     */
    public function withBody(\Stringable|string $body): self;
}
