<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\WebView\Internal\Rpc\DefaultRpcResponder;
use Boson\WebView\Internal\WebViewContextPacker;

final readonly class BindingsCreateInfo
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
         * Contains RPC context name for JavaScript communication.
         *
         * Calls the `call()` function in the specified context when calling
         * the JS code, passing the method name as the first argument and the
         * list of arguments as the second.
         *
         * ```
         * // when context is "window.my_context.example_rpc"
         * let promise = window.my_context.example_rpc.call("foo", [1, 2, 3]);
         * ```
         *
         * @var non-empty-string
         */
        public string $rpcContext = self::DEFAULT_RPC_CONTEXT,
        /**
         * Contains root context name for JavaScript function registration.
         *
         * ```
         * // when context is "example" then the
         * // `$webview->bind('foo', foo(...));` will be defined like
         *
         * window.example.foo = function(...) { <RPC_CALL> };
         * ```
         *
         * @var non-empty-string
         */
        public string $functionContext = self::DEFAULT_CONTEXT,
        /**
         * Contains context delimiter for JavaScript function registration.
         *
         * ```
         * // when delimiter is ":"
         *
         * $webview->bind('foo:some', foo(...));
         * ```
         *
         * @var non-empty-string
         */
        public string $functionDelimiter = self::DEFAULT_DELIMITER,
    ) {}
}
