<?php

declare(strict_types=1);

namespace Boson\WebView\Scheme\Internal;

use Boson\Http\Headers;
use Boson\Http\HeadersInterface;
use Boson\Http\Method;
use Boson\Http\Method\MethodFactoryInterface;
use Boson\Http\MethodInterface;
use Boson\Http\RequestInterface;
use Boson\Http\Uri\Factory\UriFactoryInterface;
use Boson\Http\UriInterface;
use Boson\Internal\Saucer\LibSaucer;
use FFI\CData;

final class LazyInitializedRequest implements RequestInterface
{
    public MethodInterface $method {
        get => $this->method ??= $this->fetchMethod();
    }

    public UriInterface $url {
        get => $this->url ??= $this->fetchUri();
    }

    public HeadersInterface $headers {
        get => $this->headers ??= $this->fetchHeaders();
    }

    public string $body {
        get => $this->body ??= $this->fetchBodyString();
    }

    public function __construct(
        private readonly LibSaucer $api,
        private readonly CData $ptr,
        private readonly UriFactoryInterface $uris,
        private readonly MethodFactoryInterface $methods,
    ) {}

    private function fetchMethod(): MethodInterface
    {
        $method = $this->fetchMethodString();

        if ($method === '') {
            return Method::Get;
        }

        return $this->methods->createMethodFromString($method);
    }

    private function fetchMethodString(): string
    {
        $method = $this->api->saucer_scheme_request_method($this->ptr);

        try {
            return \FFI::string($method);
        } finally {
            \FFI::free($method);
        }
    }

    private function fetchUri(): UriInterface
    {
        return $this->uris->createUriFromString(
            uri: $this->fetchUriString(),
        );
    }

    private function fetchUriString(): string
    {
        $url = $this->api->saucer_scheme_request_url($this->ptr);

        try {
            return \FFI::string($url);
        } finally {
            \FFI::free($url);
        }
    }

    private function fetchHeaders(): HeadersInterface
    {
        return new Headers($this->fetchHeaderLines());
    }

    /**
     * @return iterable<non-empty-string, string>
     */
    private function fetchHeaderLines(): iterable
    {
        $headers = $this->api->new('char**');
        $values = $this->api->new('char**');
        $sizes = $this->api->new('size_t');

        $this->api->saucer_scheme_request_headers(
            $this->ptr,
            \FFI::addr($headers),
            \FFI::addr($values),
            \FFI::addr($sizes),
        );

        for ($i = 0; $i < $sizes->cdata; ++$i) {
            /** @phpstan-ignore-next-line : PHPStan false-positive */
            $header = \FFI::string($headers[$i]);

            if ($header !== '') {
                /** @phpstan-ignore-next-line : PHPStan false-positive */
                yield $header => \FFI::string($values[$i]);
            }
        }
    }

    private function fetchBodyString(): string
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

    public function __destruct()
    {
        // TODO expects implementation https://github.com/saucer/bindings/pull/2
        // $this->api->saucer_scheme_request_free($this->ptr);
        \FFI::free($this->ptr);
    }
}
