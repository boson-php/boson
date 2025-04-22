<?php

declare(strict_types=1);

namespace Boson\Http;

/**
 * @template TStatusCode of int = int
 */
interface ResponseInterface
{
    /**
     * Gets status code (at least value + reason phrase) of the response
     *
     * @var StatusCodeInterface<TStatusCode>
     */
    public StatusCodeInterface $status { get; }

    /**
     * Gets HTTP headers list of the this request instance.
     */
    public HeadersInterface $headers { get; }

    /**
     * Gets body string
     */
    public string $body { get; }
}
