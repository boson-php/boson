<?php

declare(strict_types=1);

namespace Boson\Http\StatusCode;

/**
 * @api
 *
 * @phpstan-require-implements MutableStatusCodeProviderInterface
 *
 * @phpstan-import-type StatusCodeInputType from MutableStatusCodeProviderInterface
 * @phpstan-import-type MutableStatusCodeOutputType from MutableStatusCodeProviderInterface
 *
 * @phpstan-ignore trait.unused
 */
trait MutableStatusCodeProviderImpl
{
    public int $status {
        get => $this->status;
        /**
         * @param StatusCodeInputType $status
         */
        set(int $status) => self::castMutableStatusCode($status);
    }

    /**
     * @param StatusCodeInputType $status
     *
     * @return MutableStatusCodeOutputType
     */
    public static function castMutableStatusCode(int $status): int
    {
        return \max(100, \min(599, $status));
    }
}
