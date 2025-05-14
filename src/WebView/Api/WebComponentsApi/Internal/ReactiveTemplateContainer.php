<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Internal;

use Boson\WebView\Api\WebComponentsApi\Element\MutableTemplateContainerInterface;

final class ReactiveTemplateContainer extends ImmutableTemplateContainer implements
    MutableTemplateContainerInterface
{
    public string $html {
        get => parent::$html::get();
        set(string|\Stringable $html) {
            $this->ctx->eval(\sprintf(
                'innerHTML = `%s`',
                \addcslashes((string) $html, '`'),
            ));
        }
    }

    public string $text {
        get => parent::$text::get();
        set(string|\Stringable $text) {
            $this->ctx->eval(\sprintf(
                'textContent = `%s`',
                \addcslashes((string) $text, '`'),
            ));
        }
    }
}
