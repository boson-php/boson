<?php

declare(strict_types=1);

namespace Boson\WebView\Url;

use Boson\WebView\Url;

final class MemoizedUrlParser implements UrlParserInterface
{
    /**
     * Contains last parsed input uri string.
     */
    private ?string $lastUrlString = null;

    /**
     * Contains last parsed output uri instance.
     */
    private ?Url $lastUrl = null;

    public function __construct(
        private readonly UrlParserInterface $delegate,
    ) {}

    public function parse(string $url): Url
    {
        if ($url === $this->lastUrlString && $this->lastUrl !== null) {
            return $this->lastUrl;
        }

        $this->lastUrlString = $url;

        return $this->lastUrl = $this->delegate->parse($url);
    }
}
