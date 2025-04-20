<?php

declare(strict_types=1);

namespace Boson\WebView\Scripts;

use Boson\Internal\Saucer\LibSaucer;
use JetBrains\PhpStorm\Language;

final readonly class WebViewScript implements \Stringable
{
    public function __construct(
        private LibSaucer $api,
        public WebViewScriptId $id,
        #[Language('JavaScript')]
        public string $code,
        public WebViewScriptLoadingTime $time,
    ) {}

    public function __toString(): string
    {
        return $this->code;
    }

    public function __destruct()
    {
        $this->api->saucer_script_free($this->id->ptr);
    }
}
