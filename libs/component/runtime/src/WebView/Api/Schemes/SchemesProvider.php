<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes;

use Boson\Component\Http\Component\StatusCode;
use Boson\Contracts\Http\Component\StatusCodeInterface;
use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\ResponseInterface;
use Boson\Dispatcher\EventListener;
use Boson\WebView\Api\LoadedWebViewExtension;
use Boson\WebView\Api\Schemes\Event\SchemeRequestFail;
use Boson\WebView\Api\Schemes\Event\SchemeRequestReceive;
use Boson\WebView\Api\Schemes\Event\SchemeRequestReject;
use Boson\WebView\Api\Schemes\Event\SchemeResponseProceed;
use Boson\WebView\Api\Schemes\Event\SchemeResponseRejected;
use Boson\WebView\Api\Schemes\Handler\ErrorHandlerInterface;
use Boson\WebView\Api\Schemes\Request\SaucerRequestFactory;
use Boson\WebView\Api\Schemes\Request\SaucerResponseFactory;
use Boson\WebView\WebView;
use FFI\CData;

final class SchemesProvider extends LoadedWebViewExtension implements SchemesProviderInterface
{
    private const StatusCodeInterface DEFAULT_SERVER_REJECTION = StatusCode::Forbidden;
    private const StatusCodeInterface DEFAULT_SERVER_ERROR = StatusCode::InternalServerError;

    public readonly array $schemes;

    private readonly SaucerRequestFactory $requests;

    private readonly SaucerResponseFactory $responses;

    public function __construct(
        /**
         * @var list<ErrorHandlerInterface>
         */
        private readonly array $handlers,
        WebView $context,
        EventListener $listener,
    ) {
        parent::__construct($context, $listener);

        $this->requests = new SaucerRequestFactory($this->saucer);
        $this->responses = new SaucerResponseFactory($this->saucer, new MimeTypeReader());

        $this->createSchemeInterceptors(
            $this->schemes = $this->app->info->schemes,
        );
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
            );
        }
    }

    private function onSafeRequest(CData $request, CData $executor): void
    {
        $bosonRequest = $this->requests->createFromSaucerRequest($request);

        try {
            $this->handle($bosonRequest, $executor);
        } catch (\Throwable $e) {
            try {
                $this->fail($bosonRequest, $e, $executor);
            } catch (\Throwable) {
                $this->reject($bosonRequest, self::DEFAULT_SERVER_ERROR, $executor);
            }
        }
    }

    private function handle(RequestInterface $request, CData $executor): void
    {
        $processable = $this->intent($intention = new SchemeRequestReceive(
            subject: $this->webview,
            request: $request,
        ));

        // Abort request in case of intention is cancelled.
        if ($processable === false) {
            $this->reject($request, self::DEFAULT_SERVER_REJECTION, $executor);

            return;
        }

        // Do not dispatch custom response in case
        // of response is not provided.
        if (($response = $intention->response) === null) {
            return;
        }

        $this->accept($request, $response, $executor);
    }

    private function fetchFailResponse(RequestInterface $request, \Throwable $exception): ?ResponseInterface
    {
        foreach ($this->handlers as $handler) {
            $response = $handler->handle($this->webview, $request, $exception);

            if ($response instanceof ResponseInterface) {
                return $response;
            }
        }

        return null;
    }

    private function fail(RequestInterface $request, \Throwable $exception, CData $executor): void
    {
        $processable = $this->intent($intention = new SchemeRequestFail(
            subject: $this->webview,
            request: $request,
            exception: $exception,
            response: $this->fetchFailResponse($request, $exception),
        ));

        if ($processable === false) {
            $this->reject($request, self::DEFAULT_SERVER_ERROR, $executor);

            return;
        }

        if ($intention->response === null) {
            $this->reject($request, self::DEFAULT_SERVER_ERROR, $executor);

            $this->app->poller->throw($exception);

            return;
        }

        $this->accept($request, $intention->response, $executor);
    }

    private function reject(RequestInterface $request, StatusCodeInterface $status, CData $executor): void
    {
        $processable = $this->intent($intention = new SchemeRequestReject(
            subject: $this->webview,
            request: $request,
            status: $status,
        ));

        if ($processable === false) {
            $status = $intention->status;
        }

        $this->app->saucer->saucer_scheme_executor_reject($executor, $status->code);

        $this->dispatch(new SchemeResponseRejected(
            subject: $this->webview,
            request: $request,
            status: $status,
        ));
    }

    private function accept(RequestInterface $request, ResponseInterface $response, CData $executor): void
    {
        [$saucerResponse, $saucerOptionalStash] = $this->responses->createFromBosonResponse($response);

        $this->app->saucer->saucer_scheme_executor_accept($executor, $saucerResponse);

        if ($saucerOptionalStash !== null) {
            $this->app->saucer->saucer_stash_free($saucerOptionalStash);
        }

        $this->dispatch(new SchemeResponseProceed(
            subject: $this->webview,
            request: $request,
            response: $response,
        ));
    }
}
