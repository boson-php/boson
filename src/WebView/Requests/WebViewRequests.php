<?php

declare(strict_types=1);

namespace Boson\WebView\Requests;

use Boson\Shared\IdValueGenerator\IdValueGeneratorInterface;
use Boson\Shared\IdValueGenerator\IntValueGenerator;
use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Internal\Timeout;
use Boson\WebView\Requests\Exception\StalledRequestException;
use Boson\WebView\Requests\Exception\UnprocessableRequestException;
use Boson\WebView\WebView;
use JetBrains\PhpStorm\Language;

/**
 * Manages requests from PHP to JavaScript document in the WebView.
 *
 * This class provides functionality to send JavaScript code to the WebView and
 * receive responses. It handles request timeouts, response processing, and
 * maintains a registry of pending requests.
 *
 * The class uses a blocking operation pattern for request handling, ensuring
 * synchronous communication between PHP and JavaScript contexts.
 */
final class WebViewRequests
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
        /**
         * Parent WebView context
         */
        private readonly WebView $webview,
        private readonly float $timeout = self::DEFAULT_REQUEST_TIMEOUT,
    ) {
        $this->idGenerator = IntValueGenerator::createFromEnvironment();

        $this->webview->bind(self::METHOD_NAME, $this->onResponseReceived(...));
    }

    /**
     * Requests arbitrary data from webview using JavaScript code.
     *
     * This method sends JavaScript code to the WebView and waits for a response.
     * The response is processed and returned to the caller. If no response is
     * received within the timeout period, an exception is thrown.
     *
     * Example usage:
     * ```
     * $location = $requests->send('document.location');
     * ```
     *
     * @api
     *
     * @param string $code The JavaScript code to execute
     * @return mixed The response from the JavaScript execution
     * @throws UnprocessableRequestException if a response cannot be received
     * @throws StalledRequestException if the request times out
     */
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
