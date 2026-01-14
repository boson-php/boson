<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes\Request;

use Boson\Component\Saucer\SaucerInterface;
use Boson\Contracts\Http\ResponseInterface;
use Boson\Shared\Marker\RequiresDealloc;
use Boson\WebView\Api\Schemes\MimeTypeReader;
use FFI\CData;

final readonly class SaucerResponseFactory
{
    /**
     * Contains an empty stash (for empty responses) to avoid allocating it again
     */
    private CData $stash;

    public function __construct(
        private SaucerInterface $saucer,
        private MimeTypeReader $mime,
    ) {
        $this->stash = $this->saucer->saucer_stash_new_from(null, 0);
    }

    /**
     * Returns Saucer response and Saucer stash pointers
     *
     * @return array{CData, ?CData}
     */
    #[RequiresDealloc]
    public function createFromBosonResponse(ResponseInterface $response): array
    {
        $saucerStash = $this->createResponseStash($response);

        $saucerResponse = $this->createResponseWithStash($response, $saucerStash);

        $this->extendSaucerStatusCode($response, $saucerResponse);
        $this->extendSaucerHeaders($response, $saucerResponse);

        if ($saucerStash === $this->stash) {
            return [$saucerResponse, null];
        }

        return [$saucerResponse, $saucerStash];
    }

    /**
     * @return int<-2147483648, 2147483647>
     */
    private function getStatusCodeFromResponse(ResponseInterface $response): int
    {
        return \max(-2147483648, \min(2147483647, $response->status->code));
    }

    /**
     * @return non-empty-string
     */
    private function getMimeTypeFromResponse(ResponseInterface $response): string
    {
        return $this->mime->getFromResponse($response);
    }

    private function extendSaucerStatusCode(ResponseInterface $boson, CData $saucer): void
    {
        $code = $this->getStatusCodeFromResponse($boson);

        $this->saucer->saucer_scheme_response_set_status($saucer, $code);
    }

    private function extendSaucerHeaders(ResponseInterface $boson, CData $saucer): void
    {
        /**
         * @var non-empty-string $header
         * @var string $value
         */
        foreach ($boson->headers as $header => $value) {
            $this->saucer->saucer_scheme_response_append_header($saucer, $header, $value);
        }
    }

    #[RequiresDealloc]
    private function createResponseWithStash(ResponseInterface $response, CData $stash): CData
    {
        return $this->saucer->saucer_scheme_response_new(
            $stash,
            $this->getMimeTypeFromResponse($response),
        );
    }

    #[RequiresDealloc]
    private function createResponseStash(ResponseInterface $response): CData
    {
        $length = \strlen($response->body);

        if ($length === 0) {
            return $this->stash;
        }

        $string = $this->createResponseBody($response);
        $uint8Array = $this->saucer->cast('uint8_t*', \FFI::addr($string));

        return $this->saucer->saucer_stash_new_from($uint8Array, $length);
    }

    private function createResponseBody(ResponseInterface $response): CData
    {
        $length = \strlen($response->body);
        $string = $this->saucer->new("char[$length]");

        // Avoid indirect property modification
        $body = $response->body;

        \FFI::memcpy($string, $body, $length);

        return $string;
    }

    public function __destruct()
    {
        $this->saucer->saucer_stash_free($this->stash);
    }
}
