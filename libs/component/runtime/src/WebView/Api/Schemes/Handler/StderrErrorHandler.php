<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes\Handler;

use Boson\Contracts\Http\RequestInterface;
use Boson\WebView\WebView;

final readonly class StderrErrorHandler implements ErrorHandlerInterface
{
    /**
     * @var resource
     */
    public const mixed DEFAULT_ERROR_PIPE = \STDERR;

    public function __construct(
        /**
         * @var resource
         */
        private mixed $pipe = self::DEFAULT_ERROR_PIPE,
    ) {}

    public function handle(WebView $context, RequestInterface $request, \Throwable $exception): null
    {
        if (\is_resource($this->pipe)) {
            \fwrite($this->pipe, $exception . "\n");
        }

        return null;
    }
}
