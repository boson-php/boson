<?php

declare(strict_types=1);

namespace Boson\Http;

use Boson\Http\Headers\HeadersFactory;
use Boson\Http\Headers\HeadersFactoryInterface;
use Boson\Http\StatusCode\CustomStatusCode;

/**
 * @template TStatusCode of int = int
 * @template-implements ResponseInterface<TStatusCode>
 */
readonly class Response implements ResponseInterface
{
    /**
     * @var StatusCodeInterface<TStatusCode>
     */
    public StatusCodeInterface $status;

    public HeadersInterface $headers;

    /**
     * @param StatusCodeInterface<TStatusCode>|TStatusCode $status
     * @param HeadersInterface|iterable<non-empty-string, string|\Stringable> $headers
     */
    public function __construct(
        public string $body = '',
        HeadersInterface|iterable $headers = [],
        /** @phpstan-ignore-next-line : Known issue */
        StatusCodeInterface|int $status = StatusCode::OK,
        private HeadersFactoryInterface $headersFactory = new HeadersFactory(),
    ) {
        $this->headers = $this->extendHeaders(
            headers: $this->formatHeaders($headers),
        );
        $this->status = $this->formatStatusCode($status);
    }

    /**
     * Extend headers by defaults
     */
    private function extendHeaders(EvolvableHeadersInterface $headers): EvolvableHeadersInterface
    {
        // Set UTF-8 html content header in case of content-type
        // header line is not defined
        if (!$headers->contains('content-type')) {
            $headers = $headers->withHeader('content-type', 'text/html; charset=utf-8');
        }

        // Fix unnecessary content-length
        if ($headers->contains('transfer-encoding')) {
            $headers = $headers->withoutHeader('content-length');
        }

        return $headers;
    }

    /**
     * @param StatusCodeInterface<TStatusCode>|TStatusCode $status
     *
     * @return StatusCodeInterface<TStatusCode>
     */
    protected function formatStatusCode(StatusCodeInterface|int $status): StatusCodeInterface
    {
        if ($status instanceof StatusCodeInterface) {
            return $status;
        }

        /** @phpstan-ignore-next-line : Known issue */
        return StatusCode::tryFrom($status)
            ?? new CustomStatusCode($status);
    }

    /**
     * @param HeadersInterface|iterable<non-empty-string, string|\Stringable> $headers
     */
    protected function formatHeaders(iterable $headers): EvolvableHeadersInterface
    {
        if ($headers instanceof EvolvableHeadersInterface) {
            return $headers;
        }

        $immutable = $this->headersFactory->createHeadersFromIterable($headers);

        return new EvolvableHeaders($immutable);
    }
}
