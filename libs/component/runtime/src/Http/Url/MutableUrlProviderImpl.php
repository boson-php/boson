<?php

declare(strict_types=1);

namespace Boson\Http\Url;

use Boson\Http\Request;

/**
 * @api
 *
 * @phpstan-require-implements MutableUrlProviderInterface
 *
 * @phpstan-import-type UrlInputType from MutableUrlProviderInterface
 * @phpstan-import-type MutableUrlOutputType from MutableUrlProviderInterface
 *
 * @phpstan-ignore trait.unused
 */
trait MutableUrlProviderImpl
{
    /**
     * @var MutableUrlOutputType
     */
    public string $url {
        get => $this->url;
        /**
         * @param UrlInputType $url
         */
        set(string|\Stringable $url) => self::castMutableUrl($url);
    }

    /**
     * @param UrlInputType $url
     *
     * @return MutableUrlOutputType
     */
    public static function castMutableUrl(string|\Stringable $url): string
    {
        return Request::castUrl($url);
    }
}
