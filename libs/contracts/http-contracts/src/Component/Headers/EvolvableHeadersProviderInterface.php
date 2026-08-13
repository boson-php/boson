<?php

declare(strict_types=1);

namespace Boson\Contracts\Http\Component\Headers;

use Boson\Contracts\Http\Component\HeadersInterface;
use Boson\Contracts\Http\Exception\InvalidComponentArgumentExceptionInterface;

/**
 * Evolvable implementation of {@see HeadersProviderInterface}.
 *
 * Allows to modify header values using instance headers as a prototype
 * without changing the object itself.
 *
 * @phpstan-import-type InHeadersListType from HeadersInterface
 *
 * @phpstan-type InHeadersType InHeadersListType
 */
interface EvolvableHeadersProviderInterface extends HeadersProviderInterface
{
    /**
     * Return an instance with the specified message headers list
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance
     * that has the new headers list
     *
     * @param InHeadersType $headers A new HTTP headers list
     *
     * @throws InvalidComponentArgumentExceptionInterface in case of new passed
     *         headers list value is invalid or contain invalid headers
     */
    public function withHeaders(iterable $headers): self;
}
