<?php

declare(strict_types=1);

namespace Boson\Internal\Saucer;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson
 */
final readonly class SaucerState
{
    public const int SAUCER_STATE_STARTED = 0;
    public const int SAUCER_STATE_FINISHED = 1;

    private function __construct() {}
}
