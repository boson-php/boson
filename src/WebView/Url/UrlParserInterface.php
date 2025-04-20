<?php

declare(strict_types=1);

namespace Boson\WebView\Url;

use Boson\WebView\Url;

interface UrlParserInterface
{
    /**
     * Parse given URL string to an {@see Url} instance.
     */
    public function parse(string $url): Url;
}
