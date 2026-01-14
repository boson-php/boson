<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes\Handler;

use Boson\Contracts\Http\RequestInterface;
use Boson\WebView\WebView;

final readonly class StderrErrorHandler implements ErrorHandlerInterface
{
    public function handle(WebView $context, RequestInterface $request, \Throwable $exception): null
    {
        \fwrite(\STDERR, $exception . "\n");

        return null;
    }
}
