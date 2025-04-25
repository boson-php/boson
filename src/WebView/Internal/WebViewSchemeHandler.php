<?php

declare(strict_types=1);

namespace Boson\WebView\Internal;

use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Http\Method\MethodFactoryInterface;
use Boson\Http\RequestInterface;
use Boson\Http\ResponseInterface;
use Boson\Http\Uri\Factory\UriFactoryInterface;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Internal\Saucer\SaucerLaunch;
use Boson\Internal\Saucer\SaucerSchemeError;
use Boson\Shared\Marker\RequiresDealloc;
use Boson\WebView\Event\WebViewRequest;
use Boson\WebView\Internal\WebViewSchemeHandler\MimeTypeReader;
use Boson\WebView\WebView;
use FFI\CData;

final readonly class WebViewSchemeHandler
{
    private MimeTypeReader $mimeTypes;

    public function __construct(
        private LibSaucer $api,
        private WebView $webview,
        private UriFactoryInterface $uris,
        private MethodFactoryInterface $methods,
        private EventDispatcherInterface $events,
    ) {
        $this->mimeTypes = new MimeTypeReader();

        $this->createSchemeInterceptors(
            schemes: $this->webview->window->app->info->schemes,
        );
    }

    /**
     * @param iterable<mixed, non-empty-lowercase-string> $schemes
     */
    private function createSchemeInterceptors(iterable $schemes): void
    {
        foreach ($schemes as $scheme) {
            $this->api->saucer_webview_handle_scheme(
                $this->webview->window->id->ptr,
                $scheme,
                function (CData $handle, CData $request, CData $executor): void {
                    $intention = $this->events->dispatch(new WebViewRequest(
                        subject: $this->webview,
                        request: $this->createRequest($request),
                    ));

                    try {
                        // Abort request in case of intention is cancelled.
                        if ($intention->isCancelled) {
                            $code = SaucerSchemeError::SAUCER_REQUEST_ERROR_ABORTED;

                            $this->api->saucer_scheme_executor_reject($executor, $code);

                            return;
                        }

                        // Do not dispatch custom response in case
                        // of response is not provided.
                        if (($response = $intention->response) === null) {
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

    private function createRequest(CData $request): RequestInterface
    {
        return new LazyInitializedRequest(
            api: $this->api,
            ptr: $request,
            uris: $this->uris,
            methods: $this->methods,
        );
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
        $mime = $this->mimeTypes->getFromResponse($response);
        $struct = $this->api->saucer_scheme_response_new($stash, $mime);

        /** @phpstan-ignore-next-line : Allow invalid status codes */
        $this->api->saucer_scheme_response_set_status($struct, $response->status->code);

        foreach ($response->headers as $header => $value) {
            $this->api->saucer_scheme_response_add_header($struct, $header, (string) $value);
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
}
