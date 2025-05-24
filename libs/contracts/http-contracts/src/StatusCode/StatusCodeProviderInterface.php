<?php

declare(strict_types=1);

namespace Boson\Contracts\Http\StatusCode;

/**
 * @phpstan-type StatusCodeOutputType int<100, 599>
 */
interface StatusCodeProviderInterface
{
    /**
     * Gets status code integer value of the instance.
     *
     * @var StatusCodeOutputType
     */
    public int $status {
        get;
    }
}
