<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

final readonly class RequestsCreateInfo
{
    /**
     * Default timeout for request processing in seconds.
     */
    public const float DEFAULT_REQUEST_TIMEOUT = 0.1;

    /**
     * @var non-empty-string
     */
    public const string DEFAULT_CALLBACK_NAME = 'boson.respond';

    public function __construct(
        /**
         * Timeout for request processing in seconds.
         *
         * Defines how long the system will wait for a response before
         * considering the request stalled.
         */
        public float $timeout = self::DEFAULT_REQUEST_TIMEOUT,
        /**
         * JavaScript method name for response handling.
         *
         * Defines the name of the JavaScript function that will
         * be used to send responses back to PHP.
         *
         * @var non-empty-string
         */
        public string $callback = self::DEFAULT_CALLBACK_NAME,
    ) {}
}
