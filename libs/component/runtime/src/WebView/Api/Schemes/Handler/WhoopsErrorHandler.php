<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes\Handler;

use Boson\Component\Http\Response;
use Boson\Contracts\Http\Component\StatusCodeInterface;
use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\ResponseInterface;
use Boson\WebView\WebView;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as WhoopsHandler;

final readonly class WhoopsErrorHandler implements ErrorHandlerInterface
{
    private ?WhoopsHandler $handler;

    public function __construct(
        private StatusCodeInterface $status = self::DEFAULT_ERROR_CODE,
    ) {}

    private function createWhoopsHandler(): ?WhoopsHandler
    {
        if (!\class_exists(WhoopsHandler::class)) {
            return null;
        }

        $handler = new PrettyPageHandler();
        $handler->handleUnconditionally(true);

        $whoops = new WhoopsHandler();
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);
        $whoops->pushHandler($handler);

        return $whoops;
    }

    private function fetchWhoopsHandler(WebView $context): ?WhoopsHandler
    {
        if ($context->app->isDebug === false) {
            return null;
        }

        return $this->handler ??= $this->createWhoopsHandler();
    }

    public function handle(WebView $context, RequestInterface $request, \Throwable $exception): ?ResponseInterface
    {
        $handler = $this->fetchWhoopsHandler($context);

        if ($handler === null) {
            return null;
        }

        return new Response(
            body: $handler->handleException($exception),
            status: $this->status,
        );
    }
}
