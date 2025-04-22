<?php

declare(strict_types=1);

namespace Boson\Http;

interface EvolvableHeadersInterface extends HeadersInterface
{
    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * @phpstan-pure
     *
     * @param non-empty-string $name case-insensitive header field name
     */
    public function withHeader(string $name, string $value): self;

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * @phpstan-pure
     *
     * @param non-empty-string $name case-insensitive header field name to add
     */
    public function withAddedHeader(string $name, string $value): self;

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * @phpstan-pure
     *
     * @param non-empty-string $name
     */
    public function withoutHeader(string $name): self;

    /**
     * Return an instance without any headers.
     *
     * @phpstan-pure
     */
    public function withoutHeaders(): self;
}
