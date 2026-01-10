<?php

declare(strict_types=1);

namespace Boson\Component\Saucer;

final readonly class WindowEdge
{
    public const int SAUCER_WINDOW_EDGE_TOP = 1 << 0;
    public const int SAUCER_WINDOW_EDGE_BOTTOM = 1 << 1;
    public const int SAUCER_WINDOW_EDGE_LEFT = 1 << 2;
    public const int SAUCER_WINDOW_EDGE_RIGHT = 1 << 3;
    public const int SAUCER_WINDOW_EDGE_BOTTOM_LEFT = self::SAUCER_WINDOW_EDGE_BOTTOM | self::SAUCER_WINDOW_EDGE_LEFT;
    public const int SAUCER_WINDOW_EDGE_BOTTOM_RIGHT = self::SAUCER_WINDOW_EDGE_BOTTOM | self::SAUCER_WINDOW_EDGE_RIGHT;
    public const int SAUCER_WINDOW_EDGE_TOP_LEFT = self::SAUCER_WINDOW_EDGE_TOP | self::SAUCER_WINDOW_EDGE_LEFT;
    public const int SAUCER_WINDOW_EDGE_TOP_RIGHT = self::SAUCER_WINDOW_EDGE_TOP | self::SAUCER_WINDOW_EDGE_RIGHT;

    private function __construct() {}
}
