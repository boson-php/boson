<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Api\DataApi\Exception\StalledRequestException;
use Boson\WebView\Api\DataApi\Exception\UnprocessableRequestException;
use JetBrains\PhpStorm\Language;
use React\Promise\PromiseInterface;

/**
 * Managing data between PHP and JavaScript in the WebView.
 *
 * Defines the contract for sending JavaScript code to the WebView
 * and receiving responses.
 */
interface DataApiInterface
{
    /**
     * Synchronously retrieve data from the WebView using JavaScript code.
     *
     * This method sends JavaScript code to the WebView and blocks until
     * a response is received or a timeout occurs. It's suitable for simple,
     * quick operations where blocking is acceptable.
     *
     * Example usage:
     * ```
     * $location = $webview->data->get('document.location');
     * ```
     *
     * @api
     *
     * @param string $code The JavaScript code to retrieve
     *
     * @return mixed The response from the JavaScript execution
     * @throws UnprocessableRequestException if the request cannot be processed
     * @throws StalledRequestException if the request times out
     */
    #[BlockingOperation]
    public function get(#[Language('JavaScript')] string $code): mixed;

    /**
     * Asynchronously retrieve data from the WebView using JavaScript code.
     *
     * This method sends JavaScript code to the WebView and returns a promise t
     * hat resolves with the response. It's suitable for operations that
     * might take longer or when non-blocking behavior is desired.
     *
     * Example usage:
     * ```
     * $webview->data->defer('document.location')
     *     ->then(function (array $result): void {
     *         var_dump($result);
     *     });
     * ```
     *
     * @api
     *
     * @param string $code The JavaScript code to retrieve
     *
     * @return PromiseInterface<mixed> A promise that resolves with the response
     * @throws UnprocessableRequestException if the request cannot be processed
     * @throws StalledRequestException if the request times out
     */
    public function defer(#[Language('JavaScript')] string $code): PromiseInterface;
}
