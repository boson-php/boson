<?php

declare(strict_types=1);

namespace Boson\Internal\Saucer;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson
 */
final readonly class SaucerLaunch
{
    public const int SAUCER_LAUNCH_SYNC = 0;
    public const int SAUCER_LAUNCH_ASYNC = 1;

    private function __construct() {}
}
