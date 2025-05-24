<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Component;

/**
 * @phpstan-require-implements HasTemplateInterface
 */
trait HasTemplate
{
    public function render(): string
    {
        if ($this instanceof \Stringable) {
            return (string) $this;
        }

        if ($this instanceof HasShadowDomInterface) {
            return '<slot />';
        }

        return '';
    }
}
