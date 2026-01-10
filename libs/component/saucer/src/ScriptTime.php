<?php

declare(strict_types=1);

namespace Boson\Component\Saucer;

final readonly class ScriptTime
{
    public const int SAUCER_SCRIPT_TIME_CREATION = 0;
    public const int SAUCER_SCRIPT_TIME_READY = 1;

    private function __construct() {}
}
