<?php

declare(strict_types=1);

namespace Boson\WebView\Manager;

use Boson\WebView\WebView;
use Boson\WebView\WebViewCreateInfo;

interface WebViewFactoryInterface
{
    /**
     * Creates a new window webview using passed optional configuration DTO.
     */
    public function create(WebViewCreateInfo $info = new WebViewCreateInfo()): WebView;
}