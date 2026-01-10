<?php

declare(strict_types=1);

namespace Boson\Component\Saucer;

final readonly class WindowDecoration
{
    public const int SAUCER_WINDOW_DECORATION_NONE = 0;
    public const int SAUCER_WINDOW_DECORATION_PARTIAL = 1;
    public const int SAUCER_WINDOW_DECORATION_FULL = 2;

    private function __construct() {}
}
