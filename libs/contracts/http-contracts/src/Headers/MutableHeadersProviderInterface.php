<?php

declare(strict_types=1);

namespace Boson\Contracts\Http\Headers;

use Boson\Contracts\Http\MutableHeadersInterface;

/**
 * @phpstan-import-type HeadersListOutputType from HeadersProviderInterface
 *
 * @phpstan-type HeadersListInputType iterable<non-empty-string, string|iterable<mixed, string>>
 * @phpstan-type MutableHeadersListOutputType HeadersListOutputType&MutableHeadersInterface
 */
interface MutableHeadersProviderInterface extends HeadersProviderInterface
{
    /**
     * Contains default headers list definition value.
     *
     * @var HeadersListInputType
     */
    public const iterable DEFAULT_HEADERS = [];

    /**
     * Get behaviour similar to {@see HeadersProviderInterface::$headers}, but
     * returns mutable headers list instance instead of immutable.
     *
     * @var MutableHeadersListOutputType
     */
    public MutableHeadersInterface $headers {
        get;
        /**
         * Allows to set any headers list, including immutable.
         *
         * @param HeadersListInputType $headers
         */
        set(iterable $headers);
    }
}
