<?php

declare(strict_types=1);

namespace Boson\WebView;

enum WebViewState
{
    /**
     * The state indicating navigation to a new URL.
     */
    case Navigating;

    /**
     * A state indicating that data is being loaded from
     * the specified URL.
     */
    case Loading;

    /**
     * A state indicating readiness for work with document.
     */
    case Ready;
}
