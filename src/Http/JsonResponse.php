<?php

declare(strict_types=1);

namespace Boson\Http;

/**
 * @template TStatusCode of int = int
 * @template-extends Response<TStatusCode>
 */
readonly class JsonResponse extends Response
{
    /**
     * Encode <, >, ', &, and " characters in the JSON, making
     * it also safe to be embedded into HTML.
     */
    protected const int DEFAULT_JSON_ENCODING_OPTIONS = \JSON_HEX_TAG
        | \JSON_HEX_APOS
        | \JSON_HEX_AMP
        | \JSON_HEX_QUOT;

    /**
     * @param StatusCodeInterface<TStatusCode>|TStatusCode $status
     * @param HeadersInterface|iterable<non-empty-string, string> $headers
     *
     * @throws \JsonException
     */
    public function __construct(
        mixed $data = null,
        HeadersInterface|iterable $headers = [],
        /** @phpstan-ignore-next-line : Known issue */
        StatusCodeInterface|int $status = StatusCode::OK,
        /**
         * JSON encoding options
         */
        protected int $jsonEncodingOptions = self::DEFAULT_JSON_ENCODING_OPTIONS,
    ) {
        $json = $this->formatJsonBody($data);

        $headers = $this->formatHeaders($headers);
        $headers = $this->extendJsonHeaders($headers);

        parent::__construct($json, $headers, $status);
    }

    /**
     * Extend headers by the "application/json" content type
     * in case of content-type header has not been defined.
     */
    protected function extendJsonHeaders(EvolvableHeadersInterface $headers): EvolvableHeadersInterface
    {
        if ($headers->contains('content-type')) {
            return $headers;
        }

        return $headers->withHeader('content-type', 'application/json');
    }

    /**
     * Encode passed data payload to a json string.
     *
     * @throws \JsonException
     */
    protected function formatJsonBody(mixed $data): string
    {
        return \json_encode($data, $this->jsonEncodingOptions | \JSON_THROW_ON_ERROR);
    }
}
