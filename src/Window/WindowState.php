<?php

declare(strict_types=1);

namespace Boson\Window;

enum WindowState
{
    /**
     * Standard window state with custom (user defined) sizes.
     */
    case Normal;

    /**
     * Minimized (iconified) window state.
     */
    case Minimized;

    /**
     * Maximized (i.e. zoomed) window state.
     */
    case Maximized;
}
