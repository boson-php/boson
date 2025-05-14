<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Internal;

use Boson\WebView\Api\ScriptsApiInterface;
use Boson\WebView\Api\WebComponentsApi\TemplateChangerInterface;
use JetBrains\PhpStorm\Language;

final readonly class WebViewComponentTemplateChanger implements TemplateChangerInterface
{
    private const string HTML_CONTENT_TEMPLATE = <<<'JS'
        try {
            var __%s = window.boson.components.instances["%1$s"];
            if (__%1$s) {
                __%1$s.innerHTML = `%s`;
            }
        } catch (e) {
            console.error(e);
        }
        JS;

    private const string SHADOW_DOM_TEMPLATE = <<<'JS'
        try {
            var __%s = window.boson.components.instances["%1$s"];
            if (__%1$s && __%1$s.shadowRoot) {
                __%1$s.shadowRoot.innerHTML = `%s`;
            }
        } catch (e) {
            console.error(e);
        }
        JS;

    private string $template;

    public function __construct(
        private ScriptsApiInterface $scripts,
        /**
         * @var non-empty-string
         */
        private string $id,
        bool $useShadowDom,
    ) {
        $this->template = $useShadowDom
            ? self::SHADOW_DOM_TEMPLATE
            : self::HTML_CONTENT_TEMPLATE;
    }

    public function changeTemplate(#[Language('HTML')] string $html): void
    {
        $this->scripts->eval(\sprintf(
            $this->template,
            $this->id,
            \addcslashes($html, '`'),
        ));
    }
}
