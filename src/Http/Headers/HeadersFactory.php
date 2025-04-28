<?php

declare(strict_types=1);

namespace Boson\Http\Headers;

use Boson\Http\Headers;
use Boson\Http\Headers\HeaderLine\HeaderLineFactory;
use Boson\Http\Headers\HeaderLine\HeaderLineFactoryInterface;
use Boson\Http\HeadersInterface;

final readonly class HeadersFactory implements HeadersFactoryInterface
{
    public function __construct(
        private HeaderLineFactoryInterface $headerLineFactory = new HeaderLineFactory(),
    ) {}

    public function createHeadersFromIterable(iterable $headers): HeadersInterface
    {
        return new Headers($this->createAllAsIterator($headers));
    }

    /**
     * @param iterable<non-empty-string, \Stringable|string> $headers
     *
     * @return \Traversable<non-empty-lowercase-string, \Stringable|string>
     */
    private function createAllAsIterator(iterable $headers): \Traversable
    {
        foreach ($headers as $name => $value) {
            $normalized = Headers::getFormattedHeaderName($name);

            yield $normalized => $this->headerLineFactory
                ->createHeaderFromString($normalized, $value);
        }
    }
}
