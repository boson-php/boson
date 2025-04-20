<?php

declare(strict_types=1);

namespace Boson\WebView\Binding;

use Boson\Dispatcher\EventListenerInterface;
use Boson\WebView\Binding\Exception\FunctionAlreadyDefinedException;
use Boson\WebView\Binding\Exception\FunctionNotDefinedException;
use Boson\WebView\Binding\Exception\InvalidFunctionException;
use Boson\WebView\Event\WebViewMessageReceiving;
use Boson\WebView\Scripts\WebViewScriptsSet;

/**
 * @template-implements \IteratorAggregate<non-empty-string, \Closure(mixed...):mixed>
 */
final class WebViewFunctionsMap implements \IteratorAggregate, \Countable
{
    private const string BOSON_RPC = <<<'JS'
        const crypto = window.crypto || window.msCrypto;
        const saucer = window.saucer;

        class BosonRpc {
            #messages = {};

            #generateId() {
                return Array.from(crypto.getRandomValues(new Uint8Array(16)))
                    .map(b => b.toString(16).padStart(2, '0'))
                    .join('');
            }

            __resolve(id, result) {
                if (this.#messages[id]) {
                    this.#messages[id].resolve(result);
                    delete this.#messages[id];
                }
            }

            __reject(id, error) {
                if (this.#messages[id]) {
                    this.#messages[id].reject(error);
                    delete this.#messages[id];
                }
            }

            call(method, params) {
                const id = this.#generateId();

                let promise = new Promise((resolve, reject) =>
                    this.#messages[id] = {resolve, reject}
                );

                saucer.internal.send_message('%s' + JSON.stringify({id, method, params}));

                return promise;
            }
        }

        window.boson = window.boson || {};
        boson.rpc = new BosonRpc();
        JS;

    private const string BOSON_RESOLVING = <<<'JS'
        window.boson.rpc.__resolve("%s", %s);
        JS;

    private const string BOSON_REJECTION = <<<'JS'
        window.boson.rpc.__reject("%s", Error(`%s`));
        JS;

    private const string BOSON_BIND = <<<'JS'
        window["%s"] = function () {
            return window.boson.rpc.call("%1$s", Array.prototype.slice.call(arguments));
        };
        JS;

    private const string BOSON_UNBIND = <<<'JS'
        delete window["%s"];
        JS;

    /**
     * @var non-empty-string
     */
    private readonly string $prefix;

    /**
     * @var array<non-empty-string, \Closure(mixed...):mixed>
     */
    private array $functions = [];

    public function __construct(
        private readonly WebViewScriptsSet $scripts,
        private readonly EventListenerInterface $events,
    ) {
        $this->prefix = '__boson_rpc_' . \spl_object_id($this);

        $this->registerDefaultScripts();
        $this->registerDefaultEventListeners();
    }

    private function registerDefaultScripts(): void
    {
        $this->scripts->preload(\sprintf(
            self::BOSON_RPC,
            $this->prefix,
        ));
    }

    private function registerDefaultEventListeners(): void
    {
        $this->events->addEventListener(WebViewMessageReceiving::class, $this->onMessage(...));
    }

    private function onMessage(WebViewMessageReceiving $event): void
    {
        $isRpcCall = \str_starts_with($event->message, $this->prefix);

        if (!$isRpcCall) {
            return;
        }

        $json = \substr($event->message, \strlen($this->prefix));

        try {
            [
                'id' => $id,
                'method' => $method,
                'params' => $params,
            ] = (array) \json_decode($json, true, flags: \JSON_THROW_ON_ERROR);

            assert(\is_string($id) && $id !== '');
            assert(\is_string($method) && $method !== '');
            assert(\is_array($params));
        } catch (\Throwable) {
            // Could not decode RPC message
            return;
        }

        try {
            $result = $this->onCall($method, $params);

            $this->resolve($id, $result);
        } catch (\Throwable $e) {
            $this->reject($id, $e);
        }

        $event->ack();
    }

    private function reject(string $id, \Throwable $error): void
    {
        $message = \vsprintf('%s: %s in %s on line %d', [
            $error::class,
            $error->getMessage(),
            $error->getFile(),
            $error->getLine(),
        ]);

        $this->scripts->eval(\vsprintf(self::BOSON_REJECTION, [
            \addcslashes($id, '"'),
            \addcslashes($message, '`\\'),
        ]));
    }

    private function resolve(string $id, mixed $data): void
    {
        try {
            $json = \json_encode($data, \JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            $this->reject($id, $e);

            return;
        }

        $this->scripts->eval(\vsprintf(self::BOSON_RESOLVING, [
            \addcslashes($id, '"'),
            $json,
        ]));
    }

    /**
     * @param array<array-key, mixed> $params
     */
    private function onCall(string $function, array $params): mixed
    {
        if (!isset($this->functions[$function])) {
            throw InvalidFunctionException::becauseFunctionNotDefined($function);
        }

        return $this->functions[$function](...$params);
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

        $this->scripts->eval(\sprintf(
            self::BOSON_BIND,
            \addcslashes($function, '"'),
        ));
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

        $this->scripts->eval(\sprintf(
            self::BOSON_UNBIND,
            \addcslashes($function, '"'),
        ));

        unset($this->functions[$function]);
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
