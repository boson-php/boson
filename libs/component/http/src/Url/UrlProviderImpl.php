<?php

declare(strict_types=1);

namespace Boson\Component\Http\Url;

use Boson\Contracts\Http\Url\MutableUrlProviderInterface;
use Boson\Contracts\Http\Url\UrlProviderInterface;

/**
 * @api
 *
 * @phpstan-require-implements UrlProviderInterface
 *
 * @phpstan-import-type UrlInputType from MutableUrlProviderInterface
 * @phpstan-import-type UrlOutputType from UrlProviderInterface
 *
 * @phpstan-ignore trait.unused
 */
trait UrlProviderImpl
{
    /**
     * @var UrlOutputType
     */
    public readonly string $url;

    /**
     * @param UrlInputType $url
     *
     * @return UrlOutputType
     */
    public static function castUrl(string|\Stringable $url): string
    {
        if (($urlScalarValue = (string) $url) === '') {
            return MutableUrlProviderInterface::DEFAULT_URL;
        }

        return $urlScalarValue;
    }
}
