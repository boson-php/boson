<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes;

use Boson\Component\Saucer\Launch;
use Boson\Component\Saucer\SchemeError;
use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\ResponseInterface;
use Boson\Dispatcher\EventListener;
use Boson\Shared\Marker\RequiresDealloc;
use Boson\WebView\Api\LoadedWebViewExtension;
use Boson\WebView\Api\Schemes\Event\SchemeRequestReceived;
use Boson\WebView\WebView;
use FFI\CData;

final class SchemesProvider extends LoadedWebViewExtension implements SchemesProviderInterface
{
    /**
     * @var list<non-empty-lowercase-string>
     */
    public readonly array $schemes;

    private readonly MimeTypeReader $mimeTypes;

    public function __construct(WebView $context, EventListener $listener)
    {
        parent::__construct($context, $listener);

        $this->mimeTypes = new MimeTypeReader();
        $this->schemes = $this->app->info->schemes;

        $this->createSchemeInterceptors($this->schemes);
    }

    /**
     * @param iterable<mixed, non-empty-lowercase-string> $schemes
     */
    private function createSchemeInterceptors(iterable $schemes): void
    {
        foreach ($schemes as $scheme) {
            $this->app->saucer->saucer_webview_handle_scheme(
                $this->ptr,
                $scheme,
                $this->onSafeRequest(...),
                Launch::SAUCER_LAUNCH_SYNC,
            );
        }
    }

    private function onSafeRequest(CData $_, CData $request, CData $executor): void
    {
        try {
            $this->onRequest($_, $request, $executor);
        } catch (\Throwable $e) {
            $code = SchemeError::SAUCER_SCHEME_ERROR_FAILED;
            $this->app->saucer->saucer_scheme_executor_reject($executor, $code);

            $this->app->poller->throw($e);

            return;
        }
    }

    private function onRequest(CData $_, CData $request, CData $executor): void
    {
        try {
            $processable = $this->intent($intention = new SchemeRequestReceived(
                subject: $this->webview,
                request: $this->createRequest($request),
            ));

            // Abort request in case of intention is cancelled.
            if ($processable === false) {
                $code = SchemeError::SAUCER_SCHEME_ERROR_ABORTED;
                $this->app->saucer->saucer_scheme_executor_reject($executor, $code);

                return;
            }

            // Do not dispatch custom response in case
            // of response is not provided.
            if (($response = $intention->response) === null) {
                return;
            }

            $this->dispatchRequest($response, $executor);
        } finally {
            $this->app->saucer->saucer_scheme_executor_free($executor);
        }
    }

    private function createRequest(CData $request): RequestInterface
    {
        return new LazyInitializedRequest(
            api: $this->app->saucer,
            ptr: $request,
        );
    }

    private function dispatchRequest(ResponseInterface $response, CData $executor): void
    {
        $stash = $this->createResponseStash($response);
        $struct = $this->createUnmanagedResponse($response, $stash);

        $this->app->saucer->saucer_scheme_executor_resolve($executor, $struct);

        $this->app->saucer->saucer_scheme_response_free($struct);
    }

    #[RequiresDealloc]
    private function createUnmanagedResponse(ResponseInterface $response, CData $stash): CData
    {
        $mime = $this->mimeTypes->getFromResponse($response);
        $struct = $this->app->saucer->saucer_scheme_response_new($stash, $mime);

        $status = \max(-2147483648, \min(2147483647, $response->status->code));

        $this->app->saucer->saucer_scheme_response_set_status($struct, $status);

        foreach ($response->headers as $header => $value) {
            $this->app->saucer->saucer_scheme_response_add_header($struct, $header, $value);
        }

        return $struct;
    }

    #[RequiresDealloc]
    private function createResponseStash(ResponseInterface $response): CData
    {
        $length = \strlen($response->body);

        if ($length === 0) {
            $ptr = $this->app->saucer->new('uint8_t*');

            return $this->app->saucer->saucer_stash_from($ptr, 0);
        }

        $string = $this->createResponseBodyData($response);
        $uint8Array = $this->app->saucer->cast('uint8_t*', \FFI::addr($string));

        return $this->app->saucer->saucer_stash_from($uint8Array, $length);
    }

    private function createResponseBodyData(ResponseInterface $response): CData
    {
        $length = \strlen($response->body);
        $string = $this->app->saucer->new("char[$length]");

        // Avoid indirect property modification
        $body = $response->body;

        \FFI::memcpy($string, $body, $length);

        return $string;
    }
}
