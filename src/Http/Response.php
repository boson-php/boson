<?php

declare(strict_types=1);

namespace Boson\Http;

use Boson\Http\StatusCode\CustomStatusCode;

/**
 * @template TStatusCode of int = int
 * @template-implements ResponseInterface<TStatusCode>
 */
final readonly class Response implements ResponseInterface
{
    /**
     * @var StatusCodeInterface<TStatusCode>
     */
    public StatusCodeInterface $status;

    public HeadersInterface $headers;

    /**
     * @param StatusCodeInterface<TStatusCode>|TStatusCode $status
     * @param HeadersInterface|iterable<non-empty-string, string> $headers
     */
    public function __construct(
        public string $body = '',
        HeadersInterface|iterable $headers = [],
        /** @phpstan-ignore-next-line : Known issue */
        StatusCodeInterface|int $status = StatusCode::OK,
    ) {
        if (!$headers instanceof HeadersInterface) {
            $headers = new Headers($headers);
        }

        if (!$status instanceof StatusCodeInterface) {
            $status = StatusCode::tryFrom($status)
                ?? new CustomStatusCode($status);
        }

        $this->headers = $headers;
        /** @phpstan-ignore-next-line : Known issue */
        $this->status = $status;
    }
}
