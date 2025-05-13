<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Internal;

use Boson\WebView\Api\ScriptsApiInterface;
use Boson\WebView\Api\WebComponentsApi\AttributeChangerInterface;

final readonly class WebViewComponentAttributeChanger implements AttributeChangerInterface
{
    private const string ATTR_UPDATE_TEMPLATE = <<<'JS'
        const instance = window.boson.instances["%s"];

        if (instance && instance["%s"] !== %s) {
            instance["%2$s"] = %3$s;
        }
        JS;

    public function __construct(
        private ScriptsApiInterface $scripts,
        /**
         * @var non-empty-string
         */
        private string $id,
    ) {}

    public function changeAttribute(string $name, ?string $value): void
    {
        $this->scripts->eval(\sprintf(
            self::ATTR_UPDATE_TEMPLATE,
            $this->id,
            $name,
            $value,
        ));
    }
}
