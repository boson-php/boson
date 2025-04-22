<?php

declare(strict_types=1);

namespace Boson\WebView\Scheme;

use Boson\Http\Method\MethodFactoryInterface;
use Boson\Http\Middleware\MiddlewareInterface;
use Boson\Http\ResponseInterface;
use Boson\Http\Uri\Factory\UriFactoryInterface;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Internal\Saucer\SaucerLaunch;
use Boson\Shared\Marker\RequiresDealloc;
use Boson\WebView\Scheme\Internal\LazyInitializedRequest;
use Boson\WebView\WebView;
use FFI\CData;

/**
 * @template-implements \IteratorAggregate<non-empty-lowercase-string, WebViewMiddlewareSet>
 */
final readonly class WebViewSchemeSet implements \IteratorAggregate, \Countable
{
    private const string DEFAULT_CONTENT_TYPE = 'text/html';
    private const string DEFAULT_CONTENT_TYPE_SUFFIX = '; charset=utf-8';
    private const string DEFAULT_CONTENT_TYPE_PREFIX = 'text/';
    private const string DEFAULT_CONTENT_TYPE_HEADER = 'content-type';

    /**
     * @var array<non-empty-lowercase-string, WebViewMiddlewareSet>
     */
    private array $pipelines;

    public function __construct(
        private LibSaucer $api,
        private WebView $webview,
        private UriFactoryInterface $uris,
        private MethodFactoryInterface $methods,
    ) {
        $schemes = $this->webview->window->app->info->schemes;

        $this->pipelines = \iterator_to_array(
            iterator: $this->createMiddleware($schemes),
        );

        $this->createSchemeInterceptors($this->pipelines);
    }

    /**
     * @param iterable<non-empty-lowercase-string, list<MiddlewareInterface>> $schemes
     *
     * @return iterable<non-empty-lowercase-string, WebViewMiddlewareSet>
     */
    private function createMiddleware(iterable $schemes): iterable
    {
        foreach ($schemes as $scheme => $middleware) {
            yield $scheme => new WebViewMiddlewareSet($scheme, $middleware);
        }
    }

    /**
     * @param iterable<non-empty-lowercase-string, WebViewMiddlewareSet> $pipelines
     */
    private function createSchemeInterceptors(iterable $pipelines): void
    {
        foreach ($pipelines as $scheme => $pipeline) {
            $this->api->saucer_webview_handle_scheme(
                $this->webview->window->id->ptr,
                $scheme,
                function (CData $handle, CData $request, CData $executor) use ($pipeline): void {
                    $input = new LazyInitializedRequest($this->api, $request, $this->uris, $this->methods);

                    $response = $pipeline->handle($input);

                    try {
                        if ($response === null) {
                            return;
                        }

                        $this->dispatch($response, $executor);
                    } finally {
                        $this->api->saucer_scheme_executor_free($executor);
                    }
                },
                SaucerLaunch::SAUCER_LAUNCH_SYNC,
            );
        }
    }

    private function dispatch(ResponseInterface $response, CData $executor): void
    {
        $stash = $this->createResponseStash($response);
        $struct = $this->createUnmanagedResponse($response, $stash);

        $this->api->saucer_scheme_executor_resolve($executor, $struct);

        $this->api->saucer_scheme_response_free($struct);
    }

    #[RequiresDealloc]
    private function createUnmanagedResponse(ResponseInterface $response, CData $stash): CData
    {
        $mime = $this->getContentType($response);
        $struct = $this->api->saucer_scheme_response_new($stash, $mime);

        /** @phpstan-ignore-next-line : Allow invalid status codes */
        $this->api->saucer_scheme_response_set_status($struct, $response->status->code);

        foreach ($response->headers as $header => $value) {
            $this->api->saucer_scheme_response_add_header($struct, $header, $value);
        }

        // Add default charset for all "text/*" content
        if (\str_starts_with($mime, self::DEFAULT_CONTENT_TYPE_PREFIX)
            && !isset($response->headers[self::DEFAULT_CONTENT_TYPE_HEADER])
        ) {
            $this->api->saucer_scheme_response_add_header(
                $struct,
                self::DEFAULT_CONTENT_TYPE_HEADER,
                $mime . self::DEFAULT_CONTENT_TYPE_SUFFIX,
            );
        }

        return $struct;
    }

    #[RequiresDealloc]
    private function createResponseStash(ResponseInterface $response): CData
    {
        $length = \strlen($response->body);

        if ($length === 0) {
            $ptr = $this->api->new('uint8_t*');

            return $this->api->saucer_stash_from($ptr, 0);
        }

        if ($length > 32767) {
            throw new \OutOfRangeException('Response body too large');
        }

        $string = $this->createResponseBodyData($response);
        $uint8Array = $this->api->cast('uint8_t*', \FFI::addr($string));

        return $this->api->saucer_stash_from($uint8Array, $length);
    }

    private function createResponseBodyData(ResponseInterface $response): CData
    {
        $length = \strlen($response->body);
        $string = $this->api->new("char[$length]");

        // Avoid indirect property modification
        $body = $response->body;

        \FFI::memcpy($string, $body, $length);

        return $string;
    }

    private function getContentType(ResponseInterface $response): string
    {
        $headerLines = $response->headers[self::DEFAULT_CONTENT_TYPE_HEADER]
            ?? [self::DEFAULT_CONTENT_TYPE];

        foreach ($headerLines as $headerLine) {
            $headerLineSegments = \explode(';', $headerLine);

            if (($headerLineMime = \trim($headerLineSegments[0])) !== '') {
                return $headerLineMime;
            }
        }

        return self::DEFAULT_CONTENT_TYPE;
    }

    public function find(string $scheme): ?WebViewMiddlewareSet
    {
        return $this->pipelines[\strtolower($scheme)] ?? null;
    }

    public function getIterator(): \Traversable
    {
        /** @var \Traversable<non-empty-lowercase-string, WebViewMiddlewareSet> */
        return new \ArrayIterator($this->pipelines);
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return \count($this->pipelines);
    }
}
