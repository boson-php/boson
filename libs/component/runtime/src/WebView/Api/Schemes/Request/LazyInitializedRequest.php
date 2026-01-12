<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes\Request;

use Boson\Component\Http\Request;
use Boson\Component\Saucer\SaucerInterface;
use Boson\Contracts\Http\Component\HeadersInterface;
use Boson\Contracts\Http\Component\MethodInterface;
use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Uri\UriInterface;
use FFI\CData;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Scheme
 */
final class LazyInitializedRequest implements RequestInterface
{
    public MethodInterface $method {
        get => $this->method ??= Request::castMethod($this->parseRawMethodString());
    }

    public UriInterface $url {
        get => $this->url ??= Request::castUrl($this->parseRawUriString());
    }

    public HeadersInterface $headers {
        get => $this->headers ??= Request::castHeaders($this->parseRawHeadersIterable());
    }

    public string $body {
        get => $this->body ??= Request::castBody($this->parseRawBodyString());
    }

    public function __construct(
        private readonly SaucerInterface $api,
        private readonly CData $ptr,
    ) {}

    /**
     * @return non-empty-string
     */
    private function parseRawMethodString(): string
    {
        $length = $this->api->new('size_t');
        $this->api->saucer_scheme_request_method($this->ptr, null, \FFI::addr($length));

        if ($length->cdata === 0) {
            /**
             * @var non-empty-string
             *
             * @phpstan-var non-empty-uppercase-string
             */
            return (string) Request::DEFAULT_METHOD;
        }

        $method = $this->api->new('char');
        $this->api->saucer_scheme_request_method($this->ptr, \FFI::addr($method), \FFI::addr($length));

        /**
         * @var non-empty-string
         *
         * @phpstan-var non-empty-uppercase-string
         */
        return \FFI::string(\FFI::addr($method), $length->cdata);
    }

    private function parseRawUriString(): string
    {
        try {
            $ptr = $this->api->saucer_scheme_request_url($this->ptr);

            $length = $this->api->new('size_t');
            $this->api->saucer_url_string($ptr, null, \FFI::addr($length));

            if ($length->cdata === 0) {
                return '';
            }

            $url = $this->api->new('char');
            $this->api->saucer_url_string($ptr, \FFI::addr($url), \FFI::addr($length));

            return \FFI::string(\FFI::addr($url), $length->cdata);
        } finally {
            $this->api->saucer_url_free($ptr);
        }
    }

    /**
     * @return iterable<non-empty-string, string>
     */
    private function parseRawHeadersIterable(): iterable
    {
        $length = $this->api->new('size_t');
        $this->api->saucer_scheme_request_headers($this->ptr, null, \FFI::addr($length));

        if ($length->cdata === 0) {
            return [];
        }

        $value = $this->api->new('char');
        $this->api->saucer_scheme_request_headers($this->ptr, \FFI::addr($value), \FFI::addr($length));

        $header = \FFI::string(\FFI::addr($value), $length->cdata);

        foreach (\explode("\0", $header) as $headerLine) {
            [$headerName, $headerValue] = \explode(':', $headerLine, 2);

            $headerName = \trim($headerName);
            $headerValue = \trim($headerValue);

            if ($headerName !== '') {
                yield $headerName => $headerValue;
            }
        }
    }

    private function parseRawBodyString(): string
    {
        $stash = $this->api->saucer_scheme_request_content($this->ptr);
        $length = $this->api->saucer_stash_size($stash);

        try {
            if ($length <= 0) {
                return '';
            }

            $content = $this->api->saucer_stash_data($stash);

            return \FFI::string($content, $length);
        } finally {
            $this->api->saucer_stash_free($stash);
        }
    }
}
