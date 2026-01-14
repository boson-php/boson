<?php

declare(strict_types=1);

namespace Boson\Contracts\Http\Component\Headers;

use Boson\Contracts\Http\Component\HeadersInterface;

/**
 * @phpstan-type OutHeadersType HeadersInterface
 */
interface HeadersProviderInterface
{
    /**
     * Gets an immutable HTTP headers list of the instance.
     *
     * @link https://datatracker.ietf.org/doc/html/rfc4229
     *
     * @var OutHeadersType
     */
    public HeadersInterface $headers {
        get;
    }
}
