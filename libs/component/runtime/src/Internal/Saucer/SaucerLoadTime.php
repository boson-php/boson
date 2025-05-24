<?php

declare(strict_types=1);

namespace Boson\Internal\Saucer;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson
 */
final readonly class SaucerLoadTime
{
    public const int SAUCER_LOAD_TIME_CREATION = 0;
    public const int SAUCER_LOAD_TIME_READY = 1;

    private function __construct() {}
}
