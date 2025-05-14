<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Internal;

use Boson\WebView\Api\ScriptsApiInterface;
use Boson\WebView\Api\WebComponentsApi\TemplateChangerInterface;
use JetBrains\PhpStorm\Language;

final readonly class WebViewComponentTemplateChanger implements TemplateChangerInterface
{
    private const string HTML_CONTENT_TEMPLATE = <<<'JS'
        const instance = window.boson.components.instances["%s"];

        if (instance) {
            instance.innerHTML = `%s`;
        }
        JS;

    private const string SHADOW_DOM_TEMPLATE = <<<'JS'
        const instance = window.boson.components.instances["%s"];

        if (instance) {
            instance.shadowRoot.innerHTML = `%s`;
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
