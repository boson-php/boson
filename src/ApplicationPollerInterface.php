<?php

declare(strict_types=1);

namespace Boson;

/**
 * Provides window events poller for non-block operations.
 *
 * @link https://learn.microsoft.com/en-us/windows/win32/api/winuser/nf-winuser-peekmessagea
 * @link https://docs.gtk.org/glib/struct.MainLoop.html
 * @link https://en.wikipedia.org/wiki/Message_loop_in_Microsoft_Windows
 */
interface ApplicationPollerInterface
{
    /**
     * Poll next application loop event.
     */
    public function next(): bool;

    /**
     * Marks the current step as an exception.
     *
     * Required for correct processing of internal errors, for
     * throwing exceptions into the current event loop.
     */
    public function fail(\Throwable $e): void;
}
