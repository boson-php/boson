<?php

declare(strict_types=1);

namespace Boson\Http\StatusCode;

/**
 * @api
 *
 * @phpstan-require-implements StatusCodeProviderInterface
 *
 * @phpstan-import-type StatusCodeInputType from MutableStatusCodeProviderInterface
 * @phpstan-import-type StatusCodeOutputType from StatusCodeProviderInterface
 *
 * @phpstan-ignore trait.unused
 */
trait StatusCodeProviderImpl
{
    /**
     * @var StatusCodeOutputType
     */
    public readonly int $status;

    /**
     * @param StatusCodeInputType $status
     *
     * @return StatusCodeOutputType
     */
    public static function castStatusCode(int $status): int
    {
        return \max(100, \min(599, $status));
    }
}
