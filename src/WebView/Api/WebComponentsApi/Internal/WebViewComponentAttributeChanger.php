<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Internal;

use Boson\WebView\Api\ScriptsApiInterface;
use Boson\WebView\Api\WebComponentsApi\AttributeChangerInterface;

final readonly class WebViewComponentAttributeChanger implements AttributeChangerInterface
{
    private const string ATTR_UPDATE_TEMPLATE = <<<'JS'
        try {
            var __%s = window.boson.components.instances["%1$s"];
            if (__%1$s && __%1$s.getAttribute("%s") !== %s) {
                if (%3$s === null) {
                    __%1$s.removeAttribute("%2$s", %3$s);
                } else {
                    __%1$s.setAttribute("%2$s", %3$s);
                }
            }
        } catch (e) {
            console.error(e);
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
            \json_encode($value),
        ));
    }
}
