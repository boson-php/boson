<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Scripts;

use Boson\Component\Saucer\SaucerInterface;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\WebView\WebView;
use JetBrains\PhpStorm\Language;

final readonly class LoadedScript implements
    IdentifiableInterface,
    \Stringable
{
    public function __construct(
        private SaucerInterface $api,
        public WebView $webView,
        public LoadedScriptId $id,
        #[Language('JavaScript')]
        public string $code,
        public bool $isPermanent,
        public LoadedScriptLoadingTime $time,
    ) {}

    public function __toString(): string
    {
        return $this->code;
    }

    public function __destruct()
    {
        $this->api->saucer_webview_uninject(
            $this->webView->id->ptr,
            $this->id->toInteger(),
        );
    }
}
