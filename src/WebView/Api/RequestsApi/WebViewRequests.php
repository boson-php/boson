<?php

declare(strict_types=1);

namespace Boson\WebView\Api\RequestsApi;

use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Shared\IdValueGenerator\IdValueGeneratorInterface;
use Boson\Shared\IdValueGenerator\IntValueGenerator;
use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Api\ApiProvider;
use Boson\WebView\Api\RequestsApi\Exception\StalledRequestException;
use Boson\WebView\Api\RequestsApi\Exception\UnprocessableRequestException;
use Boson\WebView\Api\RequestsApiInterface;
use Boson\WebView\Internal\Timeout;
use Boson\WebView\WebView;
use JetBrains\PhpStorm\Language;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView
 */
final class WebViewRequests extends ApiProvider implements RequestsApiInterface
{
    /**
     * Default timeout for request processing in seconds.
     *
     * This constant defines how long the system will wait for a response
     * before considering the request stalled.
     */
    private const float DEFAULT_REQUEST_TIMEOUT = 0.1;

    /**
     * JavaScript method name for response handling.
     *
     * This constant defines the name of the JavaScript function that will
     * be used to send responses back to PHP.
     */
    private const string METHOD_NAME = 'boson.respond';

    /**
     * Request ID generator for tracking requests.
     *
     * This property is used to generate unique identifiers for each request,
     * allowing for proper request-response matching.
     *
     * @var IdValueGeneratorInterface<array-key>
     */
    private readonly IdValueGeneratorInterface $idGenerator;

    /**
     * Registry of pending request results.
     *
     * This array stores the results of pending requests, indexed by their
     * request IDs.
     *
     * @var array<array-key, mixed>
     */
    private array $results = [];

    public function __construct(
        LibSaucer $api,
        WebView $webview,
        EventDispatcherInterface $dispatcher,
        private readonly float $timeout = self::DEFAULT_REQUEST_TIMEOUT,
    ) {
        parent::__construct($api, $webview, $dispatcher);

        $this->idGenerator = IntValueGenerator::createFromEnvironment();

        $this->webview->bind(self::METHOD_NAME, $this->onResponseReceived(...));
    }

    #[BlockingOperation]
    public function send(#[Language('JavaScript')] string $code): mixed
    {
        if ($code === '') {
            return '';
        }

        $id = $this->idGenerator->nextId();

        $timeout = new Timeout();

        $this->webview->eval($this->pack($id, $code));

        $poller = $this->webview->window->app->poller;

        while ($poller->next()) {
            if (\array_key_exists($id, $this->results)) {
                try {
                    return $this->results[$id];
                } finally {
                    unset($this->results[$id]);
                }
            }

            if ($timeout->isExceeded($this->timeout)) {
                throw StalledRequestException::becauseRequestIsStalled($code, $this->timeout);
            }
        }

        throw UnprocessableRequestException::becauseRequestIsUnprocessable($code);
    }

    /**
     * @param array-key $id
     */
    private function pack(string|int $id, string $code): string
    {
        return \vsprintf('%s("%s", (function() { return %s; })());', [
            self::METHOD_NAME,
            \addcslashes((string) $id, '"'),
            $code,
        ]);
    }

    /**
     * Handles responses received from JavaScript.
     *
     * This method is called when a response is received from the JavaScript
     * context. It processes the response and stores it in the results registry.
     *
     * @param array-key $id The request ID
     * @param mixed $result The response data
     */
    private function onResponseReceived(string|int $id, mixed $result): void
    {
        $this->results[$id] = $result;
    }
}
