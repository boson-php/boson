<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Scripts;

use JetBrains\PhpStorm\Language;

interface ScriptEvaluatorInterface
{
    /**
     * Evaluates arbitrary JavaScript code.
     *
     * The specified JavaScript code will be executed ONCE
     * at the time the {@see exec()} method is called.
     *
     * @api
     *
     * @param string $code A JavaScript code for execution
     */
    public function eval(#[Language('JavaScript')] string $code): void;
}
