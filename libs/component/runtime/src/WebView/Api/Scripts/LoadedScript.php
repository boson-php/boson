<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Scripts;

use Boson\Internal\Saucer\LibSaucer;
use JetBrains\PhpStorm\Language;

final readonly class LoadedScript implements \Stringable
{
    public function __construct(
        private LibSaucer $api,
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
        $this->api->saucer_script_free($this->id->ptr);
    }
}
