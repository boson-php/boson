<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\WebView\Api\Scripts\LoadedScript;
use JetBrains\PhpStorm\Language;

/**
 * @template-extends \Traversable<mixed, LoadedScript>
 */
interface ScriptsApiInterface extends \Traversable, \Countable
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

    /**
     * Adds JavaScript code to execution.
     *
     * The specified JavaScript code will be executed EVERY TIME after
     * the page loads.
     *
     * @api
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
     * @api
     *
     * @param string $code A JavaScript code for execution
     */
    public function add(#[Language('JavaScript')] string $code): LoadedScript;

    /**
     * The number of registered scripts
     *
     * @return int<0, max>
     */
    public function count(): int;
}
