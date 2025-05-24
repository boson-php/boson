<?php

declare(strict_types=1);

namespace Boson\Http\Headers;

use Boson\Http\HeadersInterface;

/**
 * @phpstan-type HeadersListOutputType HeadersInterface
 */
interface HeadersProviderInterface
{
    /**
     * Gets immutable HTTP headers list of the this instance.
     *
     * @link https://datatracker.ietf.org/doc/html/rfc4229
     *
     * @var HeadersListOutputType
     */
    public HeadersInterface $headers {
        get;
    }
}
