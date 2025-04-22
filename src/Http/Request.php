<?php

declare(strict_types=1);

namespace Boson\Http;

/**
 * An implementation of immutable request instance.
 */
final readonly class Request implements RequestInterface
{
    public function __construct(
        public MethodInterface $method,
        public UriInterface $url,
        public HeadersInterface $headers,
        public string $body,
    ) {}
}
