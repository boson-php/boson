<?php

declare(strict_types=1);

namespace Boson\WebView\Api\RequestsApi;

use Boson\ApplicationPollerInterface;
use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Shared\IdValueGenerator\IdValueGeneratorInterface;
use Boson\Shared\IdValueGenerator\IntValueGenerator;
use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Api\RequestsApi\Exception\StalledRequestException;
use Boson\WebView\Api\RequestsApi\Exception\UnprocessableRequestException;
use Boson\WebView\Api\RequestsApiInterface;
use Boson\WebView\Api\RequestsCreateInfo;
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
final class WebViewRequests extends WebViewApi implements RequestsApiInterface
{
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

    /**
     * @see RequestsCreateInfo::$timeout
     */
    private readonly float $timeout;

    /**
     * @see RequestsCreateInfo::$callback
     *
     * @var non-empty-string
     */
    private readonly string $callback;

    public function __construct(
        LibSaucer $api,
        WebView $webview,
        EventDispatcherInterface $dispatcher,
    ) {
        parent::__construct($api, $webview, $dispatcher);

        $this->timeout = $webview->info->requests->timeout;
        $this->callback = $webview->info->requests->callback;

        $this->poller = $this->webview->window->app->poller;

        $this->ids = IntValueGenerator::createFromEnvironment();
        $this->webview->bind($this->callback, $this->onResponseReceived(...));
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

    public function send(#[Language('JavaScript')] string $code): PromiseInterface
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
    public function get(#[Language('JavaScript')] string $code): mixed
    {
        if ($code === '') {
            return '';
        }

        $promise = $this->send($code);

        $result = WebViewRequestsResultStatus::Pending;
        $promise->then(static function (mixed $input) use (&$result): mixed {
            return $result = $input;
        });

        $timeout = new Timeout();

        while ($this->poller->next()) {
            if ($result !== WebViewRequestsResultStatus::Pending) {
                return $result;
            }

            if (!$timeout->isExceeded($this->timeout)) {
                continue;
            }

            throw StalledRequestException::becauseRequestIsStalled($code, $this->timeout);
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
        return \vsprintf('%s("%s", (function() { return %s; })());', [
            $this->callback,
            \addcslashes((string) $id, '"'),
            $code,
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
}
