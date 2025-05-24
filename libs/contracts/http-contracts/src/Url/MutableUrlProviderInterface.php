<?php

declare(strict_types=1);

namespace Boson\Contracts\Http\Url;

/**
 * @phpstan-import-type UrlOutputType from UrlProviderInterface
 *
 * @phpstan-type UrlInputType string|\Stringable
 * @phpstan-type MutableUrlOutputType UrlOutputType
 */
interface MutableUrlProviderInterface extends UrlProviderInterface
{
    /**
     * Contains default URL definition value.
     *
     * @var UrlInputType
     */
    public const string|\Stringable DEFAULT_URL = 'about:blank';

    /**
     * Get behaviour similar to {@see UrlProviderInterface::$url}.
     *
     * @var MutableUrlOutputType
     */
    public string $url {
        get;
        /**
         * Also allows to set empty or non-normalized URL/URI string or object
         *
         * @param UrlInputType $url
         */
        set(string|\Stringable $url);
    }
}
