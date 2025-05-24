<?php

declare(strict_types=1);

namespace Boson\Contracts\Http\Body;

/**
 * @phpstan-type BodyOutputType string
 */
interface BodyProviderInterface
{
    /**
     * Gets body content string of the this instance.
     *
     * @var BodyOutputType
     */
    public string $body {
        get;
    }
}
