<?php

declare(strict_types=1);

namespace Boson\WebView\Api\FunctionsApi;

use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Internal\Saucer\LibSaucer;
use Boson\WebView\Api\WebViewApi;
use Boson\WebView\Api\FunctionsApi\Exception\FunctionAlreadyDefinedException;
use Boson\WebView\Api\FunctionsApi\Exception\FunctionNotDefinedException;
use Boson\WebView\Api\FunctionsApi\Exception\InvalidFunctionException;
use Boson\WebView\Api\FunctionsApiInterface;
use Boson\WebView\Event\WebViewMessageReceived;
use Boson\WebView\Event\WebViewNavigated;
use Boson\WebView\Internal\Rpc\DefaultRpcResponder;
use Boson\WebView\Internal\Rpc\RpcResponderInterface;
use Boson\WebView\WebView;

/**
 * @template-implements \IteratorAggregate<non-empty-string, \Closure(mixed...):mixed>
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView
 */
final class WebViewFunctionsMap extends WebViewApi implements
    FunctionsApiInterface,
    \IteratorAggregate
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
    public const string DEFAULT_CONTEXT = 'window';

    /**
     * RPC responder instance for handling JavaScript-PHP communication.
     */
    private RpcResponderInterface $responder;

    /**
     * Map of registered function names to their PHP callbacks.
     *
     * @var array<non-empty-string, \Closure(mixed...):mixed>
     */
    private array $functions = [];

    /**
     * Cache of compiled JavaScript function definitions.
     *
     * @var array<non-empty-string, non-empty-string>
     */
    private array $compiledFunctions = [];

    public function __construct(
        LibSaucer $api,
        WebView $webview,
        EventDispatcherInterface $dispatcher,
        /**
         * @var non-empty-string
         */
        private readonly string $rpcContext = self::DEFAULT_RPC_CONTEXT,
        /**
         * @var non-empty-string
         */
        private readonly string $functionContext = self::DEFAULT_CONTEXT,
    ) {
        parent::__construct($api, $webview, $dispatcher);

        $this->responder = new DefaultRpcResponder(
            scriptsApi: $this->webview->scripts,
            context: $rpcContext,
        );

        $this->registerDefaultEventListeners();
    }

    /**
     * Registers default event listeners for webview events.
     *
     * This method sets up listeners for message reception and navigation events
     * to handle function registration and RPC communication.
     */
    private function registerDefaultEventListeners(): void
    {
        $this->listen(WebViewMessageReceived::class, $this->onMessageReceived(...));
        $this->listen(WebViewNavigated::class, $this->onNavigated(...));
    }

    /**
     * Handles navigation events by re-registering all functions.
     *
     * This method is called when the webview navigates to a new page,
     * ensuring that all registered functions are available in the new context.
     */
    private function onNavigated(): void
    {
        foreach ($this->functions as $name => $callback) {
            $this->registerClientFunction($name);
        }
    }

    /**
     * Handles incoming messages from JavaScript.
     *
     * This method processes RPC calls from JavaScript, validates the message
     * format, and executes the corresponding PHP callback.
     */
    private function onMessageReceived(WebViewMessageReceived $event): void
    {
        // Skip in case of payload is not JSON
        try {
            $data = \json_decode($event->message, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return;
        }

        // Skip in case of payload did not contain id, method or params
        if (!\is_array($data) || !isset($data['id'], $data['method'], $data['params'])) {
            return;
        }

        // Skip in case of "id" is not non-empty string
        if (!\is_string($id = $data['id']) || $id === '') {
            return;
        }

        // Skip in case of "method" is not non-empty string
        if (!\is_string($method = $data['method']) || $method === '') {
            return;
        }

        // Skip in case of "params" is not array
        if (!\is_array($params = $data['params'])) {
            return;
        }

        $event->ack();

        try {
            $result = $this->call($method, $params);

            $this->responder->resolve($id, $result);
        } catch (\Throwable $e) {
            $this->responder->reject($id, $e);
        }
    }

    /**
     * Calls a registered PHP callback with the given parameters.
     *
     * @param non-empty-string $function The name of the function to call
     * @param array<array-key, mixed> $params The parameters to pass to the function
     *
     * @throws InvalidFunctionException if the function is not defined
     */
    private function call(string $function, array $params): mixed
    {
        if (!isset($this->functions[$function])) {
            throw InvalidFunctionException::becauseFunctionNotDefined($function);
        }

        return $this->functions[$function](...$params);
    }

    /**
     * Creates a JavaScript function wrapper string for RPC calls.
     *
     * @param non-empty-string $name The name of the function
     *
     * @return non-empty-string The JavaScript function definition
     */
    private function packFunction(string $name): string
    {
        return \vsprintf('function () { return %s.call("%s", Array.prototype.slice.call(arguments)); }', [
            $this->rpcContext,
            \addcslashes($name, '"'),
        ]);
    }

    /**
     * Creates a JavaScript function definition for nested object properties.
     *
     * @param non-empty-string $previous The parent object path
     * @param non-empty-string $current The current property name
     * @param non-empty-string $name The full function name
     *
     * @return non-empty-string The JavaScript function definition
     */
    private function packStackFunction(string $previous, string $current, string $name): string
    {
        return \vsprintf('%s["%s"] = %s;', [
            $previous,
            \addcslashes($current, '"'),
            $this->packFunction($name),
        ]);
    }

    /**
     * Creates a JavaScript object definition for nested properties.
     *
     * @param non-empty-string $previous The parent object path
     * @param non-empty-string $current The current property name
     *
     * @return non-empty-string The JavaScript object definition
     */
    private function packStackElement(string $previous, string $current): string
    {
        return \vsprintf('%s["%s"] = %1$s["%2$s"] || {};', [
            $previous,
            \addcslashes($current, '"'),
        ]);
    }

    /**
     * Creates a JavaScript function definition for nested namespaces.
     *
     * @param non-empty-string $name The full function name with namespace
     *
     * @return non-empty-string The JavaScript function definition
     */
    private function packNestedFunction(string $name): string
    {
        $statements = [];

        $indexAt = \substr_count($name, '.');
        $context = $this->functionContext;

        foreach (\explode('.', $name) as $index => $segment) {
            if ($segment === '') {
                continue;
            }

            $statements[] = $indexAt === $index
                ? $this->packStackFunction($context, $segment, $name)
                : $this->packStackElement($context, $segment);

            $context = $segment;
        }

        /** @var non-empty-string */
        return \implode(';', $statements);
    }

    /**
     * Registers a function in the JavaScript context.
     *
     * @param non-empty-string $name The name of the function to register
     */
    private function registerClientFunction(string $name): void
    {
        $script = $this->compiledFunctions[$name] ??= $this->packNestedFunction($name);

        $this->webview->scripts->eval($script);
    }

    public function bind(string $function, \Closure $callback): void
    {
        if (isset($this->functions[$function])) {
            throw FunctionAlreadyDefinedException::becauseFunctionAlreadyDefined($function);
        }

        $this->functions[$function] = $callback;

        $this->registerClientFunction($function);
    }

    /**
     * Unregisters a function from the JavaScript context.
     *
     * @param non-empty-string $name The name of the function to unregister
     */
    private function unregisterClientFunction(string $name): void
    {
        $this->webview->scripts->eval(\vsprintf('delete window["%s"];', [
            \addcslashes($name, '"'),
        ]));
    }

    public function unbind(string $function): void
    {
        if (!isset($this->functions[$function])) {
            throw FunctionNotDefinedException::becauseFunctionNotDefined($function);
        }

        unset($this->functions[$function], $this->compiledFunctions[$function]);

        $this->unregisterClientFunction($function);
    }

    public function getIterator(): \Traversable
    {
        /** @var \ArrayIterator<non-empty-string, \Closure(mixed...):mixed> */
        return new \ArrayIterator($this->functions);
    }

    public function count(): int
    {
        return \count($this->functions);
    }
}
