<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes\Handler;

use Boson\Component\Http\Component\StatusCode;
use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\ResponseInterface;
use Boson\WebView\WebView;

interface ErrorHandlerInterface
{
    public const StatusCodeInterface DEFAULT_ERROR_CODE = StatusCode::InternalServerError;

    public function handle(WebView $context, RequestInterface $request, \Throwable $exception): ?ResponseInterface;
}
