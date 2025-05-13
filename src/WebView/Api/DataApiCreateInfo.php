<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\Shared\IdValueGenerator\IdValueGeneratorInterface;
use Boson\Shared\IdValueGenerator\PlatformDependentIntValueGenerator;

final readonly class DataApiCreateInfo
{
    /**
     * Default timeout for request processing in seconds.
     *
     * This constant defines how long the system will wait for a response
     * before considering the request stalled.
     */
    private const float DEFAULT_TIMEOUT = 0.1;

    /**
     * JavaScript method name for response handling.
     *
     * This constant defines the name of the JavaScript function that will
     * be used to send responses back to PHP.
     */
    private const string DEFAULT_CALLBACK_METHOD = 'boson.respond';

    public function __construct(
        /**
         * Contain default timeout for response handling.
         */
        public float $timeout = self::DEFAULT_TIMEOUT,
        /**
         * Contain JavaScript method name for response handling.
         *
         * @var non-empty-string
         */
        public string $callback = self::DEFAULT_CALLBACK_METHOD,
        /**
         * Request ID generator for tracking requests.
         *
         * This property is used to generate unique identifiers for each request,
         * allowing for proper request-response matching.
         *
         * @var IdValueGeneratorInterface<array-key>
         */
        public IdValueGeneratorInterface $ids = new PlatformDependentIntValueGenerator(),
    ) {}
}
