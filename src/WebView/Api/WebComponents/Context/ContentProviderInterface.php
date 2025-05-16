<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context;

interface ContentProviderInterface
{
    /**
     * Contains inner html string.
     *
     * @link https://developer.mozilla.org/docs/Web/API/Element/innerHTML
     */
    public string $html {
        get;
    }

    /**
     * Contains inner text string.
     *
     * @link https://developer.mozilla.org/docs/Web/API/Node/textContent
     */
    public string $text {
        get;
    }
}
