<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Element;

interface MutableTemplateContainerInterface extends TemplateContainerInterface
{
    public string $html {
        get;
        /**
         * Updates inner html content
         *
         * @link https://developer.mozilla.org/docs/Web/API/Element/innerHTML
         */
        set(string|\Stringable $html);
    }

    public string $text {
        get;
        /**
         * Updates inner text content
         *
         * @link https://developer.mozilla.org/docs/Web/API/Node/textContent
         */
        set(string|\Stringable $text);
    }
}
