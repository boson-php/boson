<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Internal;

use Boson\WebView\Api\WebComponentsApi\Element\MutableAttributeMapInterface;

final readonly class ReactiveAttributeMap extends ImmutableAttributeMap implements
    MutableAttributeMapInterface
{
    public function set(string $attribute, string $value): void
    {
        $this->ctx->eval(\sprintf(
            'setAttribute("%s", "%s")',
            \addcslashes($attribute, '"'),
            \addcslashes($value, '"'),
        ));
    }

    public function remove(string $attribute): void
    {
        $this->ctx->eval(\sprintf(
            'removeAttribute("%s")',
            \addcslashes($attribute, '"'),
        ));
    }
}
