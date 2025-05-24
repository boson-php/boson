<?php

declare(strict_types=1);

namespace Boson\Contracts\Http;

interface MutableHeadersInterface extends HeadersInterface
{
    /**
     * Adds new header value replacing the specified header.
     *
     * Note: Header resolution MUST be done without case-sensitivity.
     *
     * @param non-empty-string $name case-insensitive header field name
     */
    public function set(string $name, string $value): void;

    /**
     * Adds new header value appended with the given value.
     *
     * Note: Header resolution MUST be done without case-sensitivity.
     *
     * @param non-empty-string $name case-insensitive header field name to add
     */
    public function add(string $name, string $value): void;

    /**
     * Removes the specified header.
     *
     * Note: Header resolution MUST be done without case-sensitivity.
     *
     * @param non-empty-string $name case-insensitive header field name to remove
     */
    public function remove(string $name): void;

    /**
     * Remove all headers from headers list.
     */
    public function removeAll(): void;
}
