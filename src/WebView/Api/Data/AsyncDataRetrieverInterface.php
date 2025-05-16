<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Data;

use Boson\WebView\Api\Data\Exception\ApplicationNotRunningException;
use Boson\WebView\Api\Data\Exception\StalledRequestException;
use JetBrains\PhpStorm\Language;
use React\Promise\PromiseInterface;

interface AsyncDataRetrieverInterface
{
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
     * @throws ApplicationNotRunningException if the request cannot be processed
     * @throws StalledRequestException if the request times out
     */
    public function defer(#[Language('JavaScript')] string $code): PromiseInterface;
}
