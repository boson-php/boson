<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes;

use Boson\ApplicationPollerInterface;
use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\ResponseInterface;
use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Internal\Saucer\SaucerLaunch;
use Boson\Internal\Saucer\SaucerSchemeError;
use Boson\Shared\Marker\RequiresDealloc;
use Boson\WebView\Api\SchemesApiInterface;
use Boson\WebView\Api\WebViewApi;
use Boson\WebView\Event\WebViewRequest;
use Boson\WebView\Internal\WebViewSchemeHandler\MimeTypeReader;
use Boson\WebView\WebView;
use FFI\CData;

final class WebViewSchemeHandler extends WebViewApi implements SchemesApiInterface
{
    public array $schemes;

    private readonly MimeTypeReader $mimeTypes;

    private readonly ApplicationPollerInterface $poller;

    public function __construct(LibSaucer $api, WebView $webview, EventDispatcherInterface $dispatcher)
    {
        parent::__construct($api, $webview, $dispatcher);

        $this->mimeTypes = new MimeTypeReader();

        $this->poller = $this->webview->window->app->poller;
        $this->schemes = $webview->window->app->info->schemes;

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
                $this->onSafeRequest(...),
                SaucerLaunch::SAUCER_LAUNCH_SYNC,
            );
        }
    }

    private function onSafeRequest(CData $_, CData $request, CData $executor): void
    {
        try {
            $this->onRequest($_, $request, $executor);
        } catch (\Throwable $e) {
            $code = SaucerSchemeError::SAUCER_REQUEST_ERROR_FAILED;
            $this->api->saucer_scheme_executor_reject($executor, $code);

            $this->poller->fail($e);

            return;
        }
    }

    private function onRequest(CData $_, CData $request, CData $executor): void
    {
        try {
            $processable = $this->intent($intention = new WebViewRequest(
                subject: $this->webview,
                request: $this->createRequest($request),
            ));

            // Abort request in case of intention is cancelled.
            if ($processable === false) {
                $code = SaucerSchemeError::SAUCER_REQUEST_ERROR_ABORTED;
                $this->api->saucer_scheme_executor_reject($executor, $code);

                return;
            }

            // Do not dispatch custom response in case
            // of response is not provided.
            if (($response = $intention->response) === null) {
                return;
            }

            $this->dispatchRequest($response, $executor);
        } finally {
            $this->api->saucer_scheme_executor_free($executor);
        }
    }

    private function createRequest(CData $request): RequestInterface
    {
        return new LazyInitializedRequest($this->api, $request);
    }

    private function dispatchRequest(ResponseInterface $response, CData $executor): void
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

        $this->api->saucer_scheme_response_set_status($struct, $response->status);

        foreach ($response->headers as $header => $value) {
            $this->api->saucer_scheme_response_add_header($struct, $header, $value);
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
