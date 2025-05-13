<?php

declare(strict_types=1);

namespace Boson\WebView\Api\BindingsApi;

use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Internal\Saucer\LibSaucer;
use Boson\WebView\Api\BindingsApi\Exception\FunctionAlreadyDefinedException;
use Boson\WebView\Api\BindingsApi\Exception\InvalidFunctionException;
use Boson\WebView\Api\BindingsApiInterface;
use Boson\WebView\Api\BindingsApiCreateInfo;
use Boson\WebView\Api\WebViewApi;
use Boson\WebView\Event\WebViewMessageReceived;
use Boson\WebView\Internal\Rpc\DefaultRpcResponder;
use Boson\WebView\Internal\Rpc\RpcResponderInterface;
use Boson\WebView\Internal\WebViewContextPacker;
use Boson\WebView\WebView;

/**
 * @template-implements \IteratorAggregate<non-empty-string, \Closure(mixed...):mixed>
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView
 */
final class WebViewBindingsMap extends WebViewApi implements
    BindingsApiInterface,
    \IteratorAggregate
{
    /**
     * @see BindingsApiCreateInfo::$rpcContext
     *
     * @var non-empty-string
     */
    private readonly string $rpcContext;

    /**
     * RPC responder instance for handling JavaScript-PHP communication.
     */
    private readonly RpcResponderInterface $responder;

    /**
     * Provides context packer utils
     */
    private readonly WebViewContextPacker $packer;

    /**
     * Map of registered function names to their PHP callbacks.
     *
     * @var array<non-empty-string, \Closure(mixed...):mixed>
     */
    private array $functions = [];

    public function __construct(
        LibSaucer $api,
        WebView $webview,
        EventDispatcherInterface $dispatcher,
    ) {
        parent::__construct($api, $webview, $dispatcher);

        $this->packer = new WebViewContextPacker(
            delimiter: $webview->info->bindings->functionDelimiter,
            context: $webview->info->bindings->functionContext,
        );

        $this->responder = new DefaultRpcResponder(
            scriptsApi: $this->webview->scripts,
            context: $this->rpcContext = $webview->info->bindings->rpcContext,
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
     * Registers a function in the JavaScript context.
     *
     * @param non-empty-string $name The name of the function to register
     */
    private function registerClientFunction(string $name): void
    {
        $this->webview->scripts->add($this->packer->pack(
            path: $name,
            code: $this->packFunction($name),
        ));
    }

    public function bind(string $function, \Closure $callback): void
    {
        if (isset($this->functions[$function])) {
            throw FunctionAlreadyDefinedException::becauseFunctionAlreadyDefined($function);
        }

        $this->functions[$function] = $callback;

        $this->registerClientFunction($function);
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
