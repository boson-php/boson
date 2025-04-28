<?php

declare(strict_types=1);

namespace Boson\Http\Headers;

use Boson\Http\HeadersInterface;

interface HeadersFactoryInterface
{
    /**
     * @param iterable<non-empty-string, \Stringable|string> $headers
     */
    public function createHeadersFromIterable(iterable $headers): HeadersInterface;
}
