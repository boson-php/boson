<?php

declare(strict_types=1);

namespace Boson\WebView\Api\DataApi;

use Boson\ApplicationPollerInterface;
use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Shared\IdValueGenerator\IdValueGeneratorInterface;
use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Api\DataApi\Exception\ClientErrorException;
use Boson\WebView\Api\DataApi\Exception\StalledRequestException;
use Boson\WebView\Api\DataApi\Exception\UnprocessableRequestException;
use Boson\WebView\Api\DataApiCreateInfo;
use Boson\WebView\Api\DataApiInterface;
use Boson\WebView\Api\WebViewApi;
use Boson\WebView\Internal\Timeout;
use Boson\WebView\WebView;
use JetBrains\PhpStorm\Language;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

use function React\Promise\resolve;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView
 */
final class WebViewData extends WebViewApi implements DataApiInterface
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
     * @see DataApiCreateInfo::$timeout
     */
    private readonly float $timeout;

    /**
     * @see DataApiCreateInfo::$callback
     *
     * @var non-empty-string
     */
    private readonly string $callback;

    /**
     * @see DataApiCreateInfo::$failureCallback
     *
     * @var non-empty-string
     */
    private readonly string $failureCallback;

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
     * Registry of pending request results.
     *
     * This array stores the deferred promises for pending requests,
     * indexed by their request IDs.
     *
     * @var array<array-key, Deferred<mixed>>
     */
    private array $requests = [];

    /**
     * Application poller for handling timeouts.
     */
    private readonly ApplicationPollerInterface $poller;

    public function __construct(
        LibSaucer $api,
        WebView $webview,
        EventDispatcherInterface $dispatcher,
    ) {
        parent::__construct($api, $webview, $dispatcher);

        $this->ids = $webview->info->data->ids;
        $this->timeout = $webview->info->data->timeout;
        $this->callback = $webview->info->data->callback;
        $this->failureCallback = $webview->info->data->failureCallback;
        $this->poller = $this->webview->window->app->poller;

        $this->webview->bind($this->callback, $this->onResponseReceived(...));
        $this->webview->bind($this->failureCallback, $this->onFailureReceived(...));
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

        $this->webview->eval($this->pack($id, $code));

        return $deferred->promise();
    }

    #[BlockingOperation]
    public function get(#[Language('JavaScript')] string $code, ?float $timeout = null): mixed
    {
        if ($code === '') {
            return '';
        }

        $result = WebViewRequestsResultStatus::Pending;
        $this->defer($code)
            ->then(static function (mixed $input) use (&$result): mixed {
                return $result = $input;
            })
            ->catch(static function (\Throwable $e) use (&$result): \Throwable {
                return $result = $e;
            });

        $timeout ??= $this->timeout;
        $timer = new Timeout();

        while ($this->poller->next()) {
            if ($result !== WebViewRequestsResultStatus::Pending) {
                if ($result instanceof \Throwable) {
                    throw $result;
                }

                return $result;
            }

            if (!$timer->isExceeded($timeout)) {
                continue;
            }

            throw StalledRequestException::becauseRequestIsStalled($code, $timeout);
        }

        throw UnprocessableRequestException::becauseRequestIsUnprocessable($code);
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
            $this->callback,
            $this->failureCallback,
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
