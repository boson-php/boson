<?php

declare(strict_types=1);

namespace Boson\WebView\Binding;

use Boson\Dispatcher\EventListenerInterface;
use Boson\WebView\Binding\Exception\FunctionAlreadyDefinedException;
use Boson\WebView\Binding\Exception\FunctionNotDefinedException;
use Boson\WebView\Binding\Exception\InvalidFunctionException;
use Boson\WebView\Event\WebViewMessageReceived;
use Boson\WebView\Event\WebViewNavigated;
use Boson\WebView\Internal\Rpc\DefaultRpcResponder;
use Boson\WebView\Internal\Rpc\RpcResponderInterface;
use Boson\WebView\Scripts\WebViewScriptsSet;

/**
 * @template-implements \IteratorAggregate<non-empty-string, \Closure(mixed...):mixed>
 */
final class WebViewFunctionsMap implements \IteratorAggregate, \Countable
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_RPC_CONTEXT = DefaultRpcResponder::DEFAULT_CONTEXT;

    /**
     * @var non-empty-string
     */
    public const string DEFAULT_CONTEXT = 'window';

    private RpcResponderInterface $responder;

    /**
     * @var array<non-empty-string, \Closure(mixed...):mixed>
     */
    private array $functions = [];

    /**
     * @var array<non-empty-string, non-empty-string>
     */
    private array $compiledFunctions = [];

    /**
     * @param non-empty-string $rpcContext
     */
    public function __construct(
        private readonly WebViewScriptsSet $scriptsApi,
        private readonly EventListenerInterface $events,
        /**
         * @var non-empty-string
         */
        private readonly string $rpcContext = self::DEFAULT_RPC_CONTEXT,
        private readonly string $functionContext = self::DEFAULT_CONTEXT,
    ) {
        $this->responder = new DefaultRpcResponder(
            scriptsApi: $this->scriptsApi,
            context: $rpcContext,
        );

        $this->registerDefaultEventListeners();
    }

    private function registerDefaultEventListeners(): void
    {
        $this->events->addEventListener(WebViewMessageReceived::class, $this->onMessageReceived(...));
        $this->events->addEventListener(WebViewNavigated::class, $this->onNavigated(...));
    }

    /**
     * Add all registered functions to client after navigation.
     */
    private function onNavigated(): void
    {
        foreach ($this->functions as $name => $callback) {
            $this->registerClientFunction($name);
        }
    }

    /**
     * Listen message received event.
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
        if (!isset($data['id'], $data['method'], $data['params'])) {
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
     * @param array<array-key, mixed> $params
     */
    private function call(string $function, array $params): mixed
    {
        if (!isset($this->functions[$function])) {
            throw InvalidFunctionException::becauseFunctionNotDefined($function);
        }

        return $this->functions[$function](...$params);
    }

    /**
     * @param non-empty-string $name
     * @return non-empty-string
     */
    private function packFunction(string $name): string
    {
        return \vsprintf('function () { return %s.call("%s", Array.prototype.slice.call(arguments)); }', [
            $this->rpcContext,
            \addcslashes($name, '"'),
        ]);
    }

    /**
     * @param non-empty-string $previous
     * @param non-empty-string $current
     * @param non-empty-string|null $name
     * @return non-empty-string
     */
    private function packStackFunction(string $previous, string $current, ?string $name = null): string
    {
        return \vsprintf('%s["%s"] = %s;', [
            $previous,
            \addcslashes($current, '"'),
            $this->packFunction($name ?? $current),
        ]);
    }

    /**
     * @param non-empty-string $previous
     * @param non-empty-string $current
     * @return non-empty-string
     */
    private function packStackElement(string $previous, string $current): string
    {
        return \vsprintf('%s["%s"] = %1$s["%2$s"] || {};', [
            $previous,
            \addcslashes($current, '"'),
        ]);
    }

    /**
     * @param non-empty-string $name
     * @return non-empty-string
     */
    private function packNestedFunction(string $name): string
    {
        $statements = [];

        $indexAt = \substr_count($name, '.');
        $context = $this->functionContext;

        foreach (\explode('.', $name) as $index => $segment) {
            $statements[] = $indexAt === $index
                ? $this->packStackFunction($context, $segment, $name)
                : $this->packStackElement($context, $segment);

            $context = $segment;
        }

        return \implode(';', $statements);
    }

    /**
     * @param non-empty-string $name
     */
    private function registerClientFunction(string $name): void
    {
        $script = $this->compiledFunctions[$name] ??= $this->packNestedFunction($name);

        $this->scriptsApi->eval($script);
    }

    /**
     * Binds a PHP callback to a new global JavaScript function.
     *
     * Internally, JS glue code is injected to create the JS
     * function by the given name
     *
     * @api
     *
     * @param non-empty-string $function The name of the JS function
     * @param \Closure(mixed...):mixed $callback Callback function
     *
     * @throws FunctionAlreadyDefinedException in case of function binding error
     */
    public function bind(string $function, \Closure $callback): void
    {
        if (isset($this->functions[$function])) {
            throw FunctionAlreadyDefinedException::becauseFunctionAlreadyDefined($function);
        }

        $this->functions[$function] = $callback;

        $this->registerClientFunction($function);
    }

    /**
     * @param non-empty-string $name
     */
    private function unregisterClientFunction(string $name): void
    {
        $this->scriptsApi->eval(\vsprintf('delete window["%s"];', [
            \addcslashes($name, '"'),
        ]));
    }

    /**
     * Removes a binding created with {@see WebViewFunctionsMap::bind()}
     *
     * @api
     *
     * @param non-empty-string $function The name of the JS function
     *
     * @throws FunctionNotDefinedException in case of function unbinding error
     */
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

    /**
     * The number of registered functions.
     *
     * @return int<0, max>
     */
    public function count(): int
    {
        return \count($this->functions);
    }
}
