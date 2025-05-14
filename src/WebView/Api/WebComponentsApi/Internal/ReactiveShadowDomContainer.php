<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Internal;

use Boson\WebView\Api\WebComponentsApi\Element\MutableTemplateContainerInterface;

final class ReactiveShadowDomContainer extends ImmutableShadowDomContainer implements
    MutableTemplateContainerInterface
{
    public string $html {
        get => parent::$html::get();
        set(string|\Stringable $html) {
            $this->ctx->eval(\sprintf(
                'shadowRoot.innerHTML = `%s`',
                \addcslashes((string) $html, '`'),
            ));
        }
    }

    public string $text {
        get => parent::$text::get();
        set(string|\Stringable $text) {
            $this->ctx->eval(\sprintf(
                'shadowRoot.textContent = `%s`',
                \addcslashes((string) $text, '`'),
            ));
        }
    }
}
