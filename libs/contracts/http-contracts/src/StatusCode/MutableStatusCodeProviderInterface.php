<?php

declare(strict_types=1);

namespace Boson\Contracts\Http\StatusCode;

/**
 * @phpstan-import-type StatusCodeOutputType from StatusCodeProviderInterface
 *
 * @phpstan-type StatusCodeInputType int
 * @phpstan-type MutableStatusCodeOutputType StatusCodeOutputType
 */
interface MutableStatusCodeProviderInterface extends StatusCodeProviderInterface
{
    /**
     * Contains default status code in case of status code
     * has not been passed obviously
     *
     * @var StatusCodeInputType
     */
    public const int DEFAULT_STATUS_CODE = 200;

    /**
     * Get behaviour similar to {@see StatusCodeProviderInterface::$status}.
     *
     * @var MutableStatusCodeOutputType
     */
    public int $status {
        get;
        /**
         * Allows to set any integer status code value.
         *
         * @param StatusCodeInputType $status
         */
        set(int $status);
    }
}
