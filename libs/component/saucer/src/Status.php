<?php

declare(strict_types=1);

namespace Boson\Component\Saucer;

final readonly class Status
{
    public const int SAUCER_STATE_HANDLED = 0;
    public const int SAUCER_STATE_UNHANDLED = 1;

    private function __construct() {}
}
