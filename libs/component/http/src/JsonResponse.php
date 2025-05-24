<?php

declare(strict_types=1);

namespace Boson\Component\Http;

use Boson\Contracts\Http\Body\MutableBodyProviderInterface;
use Boson\Contracts\Http\Headers\MutableHeadersProviderInterface;
use Boson\Contracts\Http\MutableHeadersInterface;
use Boson\Contracts\Http\MutableResponseInterface;
use Boson\Contracts\Http\StatusCode\MutableStatusCodeProviderInterface;

/**
 * @phpstan-type JsonEncodingFlagsType int<0, max>
 *
 * @phpstan-import-type StatusCodeInputType from MutableStatusCodeProviderInterface
 * @phpstan-import-type HeadersListInputType from MutableHeadersProviderInterface
 * @phpstan-import-type BodyInputType from MutableBodyProviderInterface
 * @phpstan-import-type MutableHeadersListOutputType from MutableHeadersProviderInterface
 */
class JsonResponse extends Response
{
    /**
     * @var non-empty-lowercase-string
     */
    protected const string DEFAULT_JSON_CONTENT_TYPE = 'application/json';

    /**
     * Encode <, >, ', &, and " characters in the JSON, making
     * it also safe to be embedded into HTML.
     *
     * @var JsonEncodingFlagsType
     */
    protected const int DEFAULT_JSON_ENCODING_FLAGS = \JSON_HEX_TAG
        | \JSON_HEX_APOS
        | \JSON_HEX_AMP
        | \JSON_HEX_QUOT;

    /**
     * @param HeadersListInputType $headers
     * @param StatusCodeInputType $status
     *
     * @throws \JsonException
     */
    public function __construct(
        mixed $data = null,
        iterable $headers = [],
        int $status = MutableResponseInterface::DEFAULT_STATUS_CODE,
        /**
         * JSON body encoding flags bit-mask.
         *
         * @var JsonEncodingFlagsType
         */
        protected int $jsonEncodingFlags = self::DEFAULT_JSON_ENCODING_FLAGS,
    ) {
        parent::__construct(
            body: $this->formatJsonBody($data),
            headers: $headers,
            status: $status,
        );
    }

    /**
     * Extend headers by the "application/json" content type
     * in case of content-type header has not been defined.
     *
     * @param MutableHeadersListOutputType $headers
     *
     * @return MutableHeadersListOutputType
     */
    #[\Override]
    protected function extendHeaders(MutableHeadersInterface $headers): MutableHeadersInterface
    {
        if (!$headers->has('content-type')) {
            $headers->add('content-type', self::DEFAULT_JSON_CONTENT_TYPE);
        }

        return parent::extendHeaders($headers);
    }

    /**
     * Encode passed data payload to a json string.
     *
     * @return BodyInputType
     * @throws \JsonException
     */
    protected function formatJsonBody(mixed $data): string|\Stringable
    {
        return \json_encode($data, $this->jsonEncodingFlags | \JSON_THROW_ON_ERROR);
    }
}
