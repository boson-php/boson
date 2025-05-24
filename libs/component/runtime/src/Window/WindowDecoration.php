<?php

declare(strict_types=1);

namespace Boson\Window;

enum WindowDecoration
{
    /**
     * Default window style.
     */
    case Default;

    /**
     * Default window style with preferred dark mode.
     */
    case DarkMode;

    /**
     * A "frameless" windows is a window which hides the default
     * window buttons & handle assigned to it by the operating system.
     *
     * Note: Please use `data-webview-drag` HTML attribute to set
     *       window draggable region.
     *
     * Note: Please use `data-webview-resize` HTML attribute to set
     *       window resize region with expected values:
     *       - `data-webview-resize="t"` - vertically, top of the window.
     *       - `data-webview-resize="b"` - vertically, bottom of the window.
     *       - `data-webview-resize="r"` - horizontally, right of the window.
     *       - `data-webview-resize="l"` - horizontally, left of the window.
     *       - Using combinations such as `tl`, `tr`, `bl` and `br` allows you
     *         to specify simultaneous resizing of the window vertically and
     *         horizontally.
     *
     * Note: Please use `data-webview-ignore` attribute to ignore handling
     *       of parent behaviour.
     *
     * ```
     * <header data-webview-drag>
     *     <span>Title Bar</span>
     *     <aside data-webview-ignore>
     *         <button>close</button>
     *     </aside>
     * </header>
     * ```
     */
    case Frameless;

    /**
     * Enables {@see Frameless} mode and disables window color.
     *
     * To control the window background, use the standard CSS and HTML features:
     * ```
     *  <body style="
     *      border-radius: 10px;
     *      background: rgba(255, 255, 255, .8);
     *  ">
     *      body
     *  </body>
     * ```
     */
    case Transparent;
}
