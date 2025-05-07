<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Api\RequestsApi\Exception\StalledRequestException;
use Boson\WebView\Api\RequestsApi\Exception\UnprocessableRequestException;
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
interface RequestsApiInterface
{
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
     *
     * @return mixed The response from the JavaScript execution
     * @throws UnprocessableRequestException if a response cannot be received
     * @throws StalledRequestException if the request times out
     */
    #[BlockingOperation]
    public function send(#[Language('JavaScript')] string $code): mixed;
}
