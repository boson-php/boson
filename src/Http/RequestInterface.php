<?php

declare(strict_types=1);

namespace Boson\Http;

interface RequestInterface
{
    /**
     * Gets HTTP method of the this request instance.
     */
    public MethodInterface $method { get; }

    /**
     * Gets URI of the this request instance.
     */
    public UriInterface $url { get; }

    /**
     * Gets HTTP headers list of the this request instance.
     */
    public HeadersInterface $headers { get; }

    /**
     * Gets content string of the this request instance.
     */
    public string $body { get; }
}
