<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Factory;

use Boson\Http\Uri;
use Boson\Http\UriInterface;

final class MemoizedUriFactory implements UriFactoryInterface
{
    /**
     * Contains last parsed input uri string.
     */
    private ?string $lastInputUriString = null;

    /**
     * Contains last parsed output uri instance.
     */
    private ?UriInterface $lastOutputUriString = null;

    public function __construct(
        private readonly UriFactoryInterface $delegate,
    ) {}

    public function createUriFromString(string $uri): UriInterface
    {
        if ($uri === $this->lastInputUriString && $this->lastOutputUriString !== null) {
            return $this->lastOutputUriString;
        }

        $this->lastInputUriString = $uri;

        return $this->lastOutputUriString = $this->delegate->createUriFromString($uri);
    }
}
