<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Scripts;

use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Internal\Saucer\LibSaucer;
use Boson\Internal\Saucer\SaucerLoadTime;
use Boson\WebView\Api\ScriptsApiInterface;
use Boson\WebView\Api\WebViewApi;
use Boson\WebView\WebView;
use JetBrains\PhpStorm\Language;

/**
 * @template-implements \IteratorAggregate<mixed, LoadedScript>
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView
 */
final class WebViewScriptsSet extends WebViewApi implements
    ScriptsApiInterface,
    \IteratorAggregate
{
    /**
     * List of loaded scripts.
     *
     * @var \SplObjectStorage<LoadedScript, mixed>
     */
    private readonly \SplObjectStorage $scripts;

    public function __construct(LibSaucer $api, WebView $webview, EventDispatcherInterface $dispatcher)
    {
        $this->scripts = new \SplObjectStorage();

        parent::__construct($api, $webview, $dispatcher);
    }

    public function eval(#[Language('JavaScript')] string $code): void
    {
        if (\trim($code) === '') {
            return;
        }

        $this->api->saucer_webview_execute($this->ptr, $code);
    }

    public function preload(#[Language('JavaScript')] string $code, bool $permanent = false): LoadedScript
    {
        $handle = $this->api->saucer_script_new($code, SaucerLoadTime::SAUCER_LOAD_TIME_CREATION);

        if ($permanent) {
            $this->api->saucer_script_set_permanent($handle, true);
        }

        return $this->registerAndInject(new LoadedScript(
            api: $this->api,
            id: LoadedScriptId::fromScriptHandle($this->api, $handle),
            code: $code,
            isPermanent: $permanent,
            time: LoadedScriptLoadingTime::OnCreated,
        ));
    }

    public function add(#[Language('JavaScript')] string $code): LoadedScript
    {
        $handle = $this->api->saucer_script_new($code, SaucerLoadTime::SAUCER_LOAD_TIME_READY);

        return $this->registerAndInject(new LoadedScript(
            api: $this->api,
            id: LoadedScriptId::fromScriptHandle($this->api, $handle),
            code: $code,
            isPermanent: false,
            time: LoadedScriptLoadingTime::OnReady,
        ));
    }

    private function registerAndInject(LoadedScript $script): LoadedScript
    {
        $this->scripts->attach($script);

        $this->api->saucer_webview_inject($this->ptr, $script->id->ptr);

        return $script;
    }

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
