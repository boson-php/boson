<?php

declare(strict_types=1);

namespace Boson\Http;

use Boson\Contracts\Http\Body\MutableBodyProviderInterface;
use Boson\Contracts\Http\Headers\MutableHeadersProviderInterface;
use Boson\Contracts\Http\MutableHeadersInterface;
use Boson\Contracts\Http\MutableResponseInterface;
use Boson\Contracts\Http\StatusCode\MutableStatusCodeProviderInterface;
use Boson\Http\Body\MutableBodyProviderImpl;
use Boson\Http\Headers\MutableHeadersProviderImpl;
use Boson\Http\StatusCode\MutableStatusCodeProviderImpl;

/**
 * @phpstan-import-type StatusCodeInputType from MutableStatusCodeProviderInterface
 * @phpstan-import-type HeadersListInputType from MutableHeadersProviderInterface
 * @phpstan-import-type BodyInputType from MutableBodyProviderInterface
 * @phpstan-import-type MutableHeadersListOutputType from MutableHeadersProviderInterface
 */
class Response implements MutableResponseInterface
{
    use MutableBodyProviderImpl;
    use MutableHeadersProviderImpl;
    use MutableStatusCodeProviderImpl;

    /**
     * @param BodyInputType $body
     * @param HeadersListInputType $headers
     * @param StatusCodeInputType $status
     */
    public function __construct(
        string|\Stringable $body = MutableBodyProviderInterface::DEFAULT_BODY,
        iterable $headers = MutableHeadersProviderInterface::DEFAULT_HEADERS,
        int $status = MutableStatusCodeProviderInterface::DEFAULT_STATUS_CODE,
    ) {
        $this->body = self::castMutableBody($body);
        $this->headers = $this->extendHeaders(
            headers: self::castMutableHeaders($headers),
        );
        $this->status = self::castMutableStatusCode($status);
    }

    /**
     * Extend headers by defaults.
     *
     * @param MutableHeadersListOutputType $headers
     *
     * @return MutableHeadersListOutputType
     */
    protected function extendHeaders(MutableHeadersInterface $headers): MutableHeadersInterface
    {
        // Set UTF-8 text/html content header in case of
        // content-type header line is not defined.
        if (!$headers->has('content-type')) {
            $headers->add('content-type', 'text/html; charset=utf-8');
        }

        // Fix unnecessary content-length.
        if ($headers->has('transfer-encoding')) {
            $headers->remove('content-length');
        }

        return $headers;
    }
}
