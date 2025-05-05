<?php

declare(strict_types=1);

namespace Boson\Window;

enum WindowCorner
{
    /**
     * Top-right corner.
     */
    case TopRight;

    /**
     * Bottom-right corner.
     */
    case BottomRight;

    /**
     * Bottom-left corner.
     */
    case BottomLeft;

    /**
     * Top-left corner.
     */
    case TopLeft;
}
