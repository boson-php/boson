<?php

declare(strict_types=1);

namespace Boson\Contracts\Http\Body;

/**
 * @phpstan-import-type BodyOutputType from BodyProviderInterface
 *
 * @phpstan-type BodyInputType string|\Stringable
 * @phpstan-type MutableBodyOutputType BodyOutputType
 */
interface MutableBodyProviderInterface extends BodyProviderInterface
{
    /**
     * Contains default body definition value.
     *
     * @var BodyInputType
     */
    public const string|\Stringable DEFAULT_BODY = '';

    /**
     * Get behaviour similar to {@see BodyProviderInterface::$body}.
     *
     * @var MutableBodyOutputType
     */
    public string $body {
        get;
        /**
         * Allows to set any string or string-like body value.
         *
         * @param BodyInputType $body
         */
        set(string|\Stringable $body);
    }
}
