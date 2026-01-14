<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes\Handler;

use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\ResponseInterface;
use Boson\WebView\WebView;

interface ErrorHandlerInterface
{
    public function handle(WebView $context, RequestInterface $request, \Throwable $exception): ?ResponseInterface;
}
