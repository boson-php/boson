<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Data;

use Boson\Dispatcher\EventListener;
use Boson\Poller\PollerInterface;
use Boson\Shared\IdValueGenerator\IdValueGeneratorInterface;
use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Api\Bindings\BindingsApiInterface;
use Boson\WebView\Api\Data\Exception\ApplicationNotRunningException;
use Boson\WebView\Api\Data\Exception\ClientErrorException;
use Boson\WebView\Api\Data\Exception\StalledRequestException;
use Boson\WebView\Api\Data\Exception\WebViewIsNotReadyException;
use Boson\WebView\Api\LoadedWebViewExtension;
use Boson\WebView\Api\Scripts\ScriptsApiInterface;
use Boson\WebView\WebView;
use Boson\WebView\WebViewState;
use JetBrains\PhpStorm\Language;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

use function React\Promise\resolve;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView
 */
final class DataRetriever extends LoadedWebViewExtension implements
    DataRetrieverInterface
{
    private const string DATA_REQUEST_TEMPLATE = <<<'JS'
        try {
            var __result%s = (function() {
                return %s;
            })();

            if (__result%1$s instanceof Promise) {
                __result%1$s.then(data => %s("%1$s", data));
            } else {
                %3$s("%1$s", __result%1$s);
            }
        } catch (e) {
            %s("%1$s", e.message);
        }
        JS;

    /**
     * Request ID generator for tracking requests.
     *
     * This property is used to generate unique identifiers for each request,
     * allowing for proper request-response matching.
     *
     * @var IdValueGeneratorInterface<array-key>
     */
    private readonly IdValueGeneratorInterface $ids;

    /**
     * An application events poller interface.
     */
    private readonly PollerInterface $poller;

    /**
     * Registry of pending request results.
     *
     * This array stores the deferred promises for pending requests,
     * indexed by their request IDs.
     *
     * @var array<array-key, Deferred<mixed>>
     */
    private array $requests = [];

    public function __construct(
        WebView $context,
        EventListener $listener,
        private readonly DataExtensionCreateInfo $info,
        private readonly ScriptsApiInterface $scripts,
        BindingsApiInterface $bindings,
    ) {
        parent::__construct($context, $listener);

        $this->ids = clone $info->ids;
        $this->poller = $this->app->poller;

        $bindings->bind($info->callback, $this->onResponseReceived(...));
        $bindings->bind($info->failureCallback, $this->onFailureReceived(...));
    }

    /**
     * Retrieves and removes a pending request from the registry.
     *
     * @param array-key $id The request ID to retrieve
     *
     * @return Deferred<mixed>|null The deferred promise for the request, or
     *        {@see null} if not found
     */
    private function pull(string|int $id): ?Deferred
    {
        try {
            return $this->requests[$id] ?? null;
        } finally {
            unset($this->requests[$id]);
        }
    }

    public function defer(#[Language('JavaScript')] string $code): PromiseInterface
    {
        if ($code === '') {
            return resolve('');
        }

        $id = $this->ids->nextId();

        $this->requests[$id] = $deferred = new Deferred(function () use ($id) {
            $this->pull($id);
        });

        $this->scripts->eval($this->pack($id, $code));

        return $deferred->promise();
    }

    #[BlockingOperation]
    public function get(#[Language('JavaScript')] string $code, ?float $timeout = null): mixed
    {
        if ($code === '') {
            return '';
        }

        if ($this->webview->state === WebViewState::Navigating) {
            throw WebViewIsNotReadyException::becauseWebViewIsNotReady($code);
        }

        if ($this->app->isRunning === false) {
            throw ApplicationNotRunningException::becauseApplicationNotRunning($code);
        }

        $suspension = $this->poller->createSuspension();

        $this->defer($code)
            ->then(static function (mixed $input) use ($suspension): mixed {
                $suspension->resolve($input);

                return $input;
            })
            ->catch(static function (\Throwable $e) use ($suspension): \Throwable {
                $suspension->reject($e);

                return $e;
            });

        $timeout ??= $this->info->timeout;

        $delayId = $this->poller->delay($timeout, function () use ($code, $timeout): void {
            throw StalledRequestException::becauseRequestIsStalled($code, $timeout);
        });

        try {
            return $suspension->suspend();
        } finally {
            $this->poller->cancel($delayId);
        }
    }

    /**
     * Creates a JavaScript function call string for sending requests.
     *
     * @param array-key $id The request ID
     * @param string $code The JavaScript code to execute
     *
     * @return string The formatted JavaScript function call
     */
    private function pack(string|int $id, string $code): string
    {
        return \vsprintf(self::DATA_REQUEST_TEMPLATE, [
            \addcslashes((string) $id, '"'),
            $code,
            $this->info->callback,
            $this->info->failureCallback,
        ]);
    }

    /**
     * Handles responses received from JavaScript.
     *
     * This method is called when a response is received from the JavaScript
     * context. It processes the response and resolves the corresponding promise.
     *
     * @param array-key $id The request ID
     * @param mixed $result The response data
     */
    private function onResponseReceived(string|int $id, mixed $result): void
    {
        if (($deferred = $this->pull($id)) === null) {
            return;
        }

        $deferred->resolve($result);
    }

    /**
     * Handles failure responses received from JavaScript.
     *
     * @param array-key $id The request ID
     * @param string $error The response error message
     */
    private function onFailureReceived(string|int $id, string $error): void
    {
        if (($deferred = $this->pull($id)) === null) {
            return;
        }

        $deferred->reject(new ClientErrorException($error));
    }
}
