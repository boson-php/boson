<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\WebView\Internal\Rpc\DefaultRpcResponder;
use Boson\WebView\Internal\WebViewContextPacker;

final readonly class FunctionsCreateInfo
{
    /**
     * Default RPC context name for JavaScript communication.
     *
     * This constant defines the default context (variable name) used for
     * RPC communication between JavaScript and PHP.
     *
     * Context name defined in the {@link ./resources/src/main.ts} source.
     *
     * @var non-empty-string
     */
    public const string DEFAULT_RPC_CONTEXT = DefaultRpcResponder::DEFAULT_CONTEXT;

    /**
     * Default context name for JavaScript function registration.
     *
     * This constant defines the default context (window) where JavaScript
     * functions will be registered.
     *
     * @var non-empty-string
     */
    public const string DEFAULT_CONTEXT = WebViewContextPacker::DEFAULT_ROOT_CONTEXT;

    /**
     * Default context delimiter for JavaScript function registration.
     *
     * @var non-empty-string
     */
    public const string DEFAULT_DELIMITER = WebViewContextPacker::DEFAULT_DELIMITER;

    public function __construct(
        /**
         * @var non-empty-string
         */
        public string $functionContext = self::DEFAULT_CONTEXT,
        /**
         * @var non-empty-string
         */
        public string $functionDelimiter = self::DEFAULT_DELIMITER,
        /**
         * @var non-empty-string
         */
        public string $rpcContext = self::DEFAULT_RPC_CONTEXT,
    ) {}
}
