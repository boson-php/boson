<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Factory;

use Boson\Http\UriInterface;

interface UriFactoryInterface
{
    /**
     * Parse given URL string to an {@see UriInterface} instance.
     */
    public function createUriFromString(string $uri): UriInterface;
}
