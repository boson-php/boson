<?php

declare(strict_types=1);

namespace Boson\WebView\Scripts;

use Boson\Internal\Saucer\LibSaucer;
use Boson\Internal\Saucer\SaucerLoadTime;
use Boson\WebView\WebView;
use FFI\CData;
use JetBrains\PhpStorm\Language;

/**
 * @template-implements \IteratorAggregate<mixed, WebViewScript>
 */
final readonly class WebViewScriptsSet implements \IteratorAggregate, \Countable
{
    /**
     * List of loaded scripts.
     *
     * @var \SplObjectStorage<WebViewScript, mixed>
     */
    private \SplObjectStorage $scripts;

    /**
     * An internal window handle pointer.
     */
    private CData $ptr;

    public function __construct(
        private LibSaucer $api,
        private WebView $webview,
    ) {
        $this->scripts = new \SplObjectStorage();
        $this->ptr = $this->webview->window->id->ptr;
    }

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
    public function eval(#[Language('JavaScript')] string $code): void
    {
        $this->api->saucer_webview_execute($this->ptr, $code);
    }

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
    public function preload(#[Language('JavaScript')] string $code, bool $permanent = false): WebViewScript
    {
        $handle = $this->api->saucer_script_new($code, SaucerLoadTime::SAUCER_LOAD_TIME_CREATION);

        if ($permanent) {
            $this->api->saucer_script_set_permanent($handle, true);
        }

        $this->registerAndInject($script = new WebViewScript(
            api: $this->api,
            id: WebViewScriptId::fromScriptHandle($this->api, $handle),
            code: $code,
            isPermanent: $permanent,
            time: WebViewScriptLoadingTime::OnCreated,
        ));

        return $script;
    }

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
    public function add(#[Language('JavaScript')] string $code): WebViewScript
    {
        $handle = $this->api->saucer_script_new($code, SaucerLoadTime::SAUCER_LOAD_TIME_READY);

        $this->registerAndInject($script = new WebViewScript(
            api: $this->api,
            id: WebViewScriptId::fromScriptHandle($this->api, $handle),
            code: $code,
            isPermanent: false,
            time: WebViewScriptLoadingTime::OnReady,
        ));

        return $script;
    }

    private function registerAndInject(WebViewScript $script): void
    {
        $this->scripts->attach($script);

        $this->inject($script);
    }

    private function inject(WebViewScript $script): void
    {
        $this->api->saucer_webview_inject($this->ptr, $script->id->ptr);
    }

    /**
     * The number of registered scripts
     *
     * @return int<0, max>
     */
    public function count(): int
    {
        return \count($this->scripts);
    }

    public function getIterator(): \Traversable
    {
        return $this->scripts;
    }

    public function __destruct()
    {
        $this->api->saucer_webview_clear_scripts($this->ptr);
    }
}
