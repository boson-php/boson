<?php

declare(strict_types=1);

namespace Boson\Window;

enum WindowDecoration
{
    /**
     * The default window decorations
     *
     * Titlebar:   ✔
     * Resizable:  ✔
     * Aero-Snap:  ✔
     * Shadows:    ✔
     */
    case Full;

    /**
     * A "frameless" windows is a window which hides the default window
     * buttons & handle assigned to it by the operating system.
     *
     * Titlebar:   ✖
     * Resizable:  ✔
     * Aero-Snap:  ✔
     * Shadows:    ✔
     */
    case Frameless;

    /**
     * Titlebar:   ✖
     * Resizable:  ✖
     * Aero-Snap:  ✖
     * Shadows:    ✖
     *
     * Note: Using {@see WindowDecoration::None} can make the app feel alien,
     *       because things like window shadows and snapping are disabled and
     *       have to be implemented by yourself.
     */
    case None;

    public const self DEFAULT = self::Full;
}
