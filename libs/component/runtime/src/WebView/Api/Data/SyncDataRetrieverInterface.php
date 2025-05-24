<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Data;

use Boson\Shared\Marker\BlockingOperation;
use Boson\WebView\Api\Data\Exception\ApplicationNotRunningException;
use Boson\WebView\Api\Data\Exception\StalledRequestException;
use JetBrains\PhpStorm\Language;

interface SyncDataRetrieverInterface
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
     * @throws ApplicationNotRunningException if the request cannot be processed
     * @throws StalledRequestException if the request times out
     */
    #[BlockingOperation]
    public function get(#[Language('JavaScript')] string $code, ?float $timeout = null): mixed;
}
