<?php

declare(strict_types=1);

namespace Boson\Internal\Saucer;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson
 */
final readonly class SaucerWindowEvent
{
    /**
     * Called when the Window decorations change.
     */
    public const int SAUCER_WINDOW_EVENT_DECORATED = 0;

    /**
     * Called when the Window is maximized or restored after being maximized.
     */
    public const int SAUCER_WINDOW_EVENT_MAXIMIZE = 1;

    /**
     * Called when the Window is minimized or restored after being minimized.
     */
    public const int SAUCER_WINDOW_EVENT_MINIMIZE = 2;

    /**
     * Called when the Window is closed.
     */
    public const int SAUCER_WINDOW_EVENT_CLOSED = 3;

    /**
     * Called when the Window is resized.
     */
    public const int SAUCER_WINDOW_EVENT_RESIZE = 4;

    /**
     * Called when the Window gains or loses focus.
     */
    public const int SAUCER_WINDOW_EVENT_FOCUS = 5;

    /**
     * Called when the Window is about to be closed.
     */
    public const int SAUCER_WINDOW_EVENT_CLOSE = 6;

    private function __construct() {}
}
