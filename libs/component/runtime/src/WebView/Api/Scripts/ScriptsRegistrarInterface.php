<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Scripts;

use JetBrains\PhpStorm\Language;

interface ScriptsRegistrarInterface
{
    /**
     * Adds JavaScript code to execution.
     *
     * The specified JavaScript code will be executed EVERY TIME after
     * the page loads.
     *
     * @param string $code A JavaScript code for execution
     */
    public function preload(#[Language('JavaScript')] string $code): LoadedScript;

    /**
     * Adds JavaScript code to execution.
     *
     * The specified JavaScript code will be executed EVERY TIME after
     * the entire DOM is loaded.
     *
     * @param string $code A JavaScript code for execution
     */
    public function add(#[Language('JavaScript')] string $code): LoadedScript;
}
