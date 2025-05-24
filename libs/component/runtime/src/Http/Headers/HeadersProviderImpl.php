<?php

declare(strict_types=1);

namespace Boson\Http\Headers;

use Boson\Contracts\Http\Headers\HeadersProviderInterface;
use Boson\Contracts\Http\Headers\MutableHeadersProviderInterface;
use Boson\Contracts\Http\HeadersInterface;
use Boson\Http\HeadersMap;

/**
 * @api
 *
 * @phpstan-require-implements HeadersProviderInterface
 *
 * @phpstan-import-type HeadersListInputType from MutableHeadersProviderInterface
 * @phpstan-import-type HeadersListOutputType from HeadersProviderInterface
 *
 * @phpstan-ignore trait.unused
 */
trait HeadersProviderImpl
{
    /**
     * @var HeadersListOutputType
     */
    public readonly HeadersInterface $headers;

    /**
     * @param HeadersListInputType $headers
     *
     * @return HeadersListOutputType
     */
    public static function castHeaders(iterable $headers): HeadersInterface
    {
        if ($headers instanceof HeadersInterface) {
            return $headers;
        }

        return HeadersMap::createFromIterable($headers);
    }
}
