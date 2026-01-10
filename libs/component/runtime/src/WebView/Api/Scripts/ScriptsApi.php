<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Scripts;

use Boson\Component\Saucer\ScriptTime;
use Boson\Dispatcher\EventListener;
use Boson\WebView\Api\LoadedWebViewExtension;
use Boson\WebView\WebView;
use JetBrains\PhpStorm\Language;

/**
 * @template-implements \IteratorAggregate<mixed, LoadedScript>
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView
 */
final class ScriptsApi extends LoadedWebViewExtension implements
    ScriptsApiInterface,
    \IteratorAggregate
{
    /**
     * List of loaded scripts.
     *
     * @var \SplObjectStorage<LoadedScript, mixed>
     */
    private readonly \SplObjectStorage $scripts;

    public function __construct(WebView $context, EventListener $listener)
    {
        parent::__construct($context, $listener);

        $this->scripts = new \SplObjectStorage();
    }

    public function eval(#[Language('JavaScript')] string $code): void
    {
        if (\trim($code) === '') {
            return;
        }

        $this->app->saucer->saucer_webview_execute($this->ptr, $code);
    }

    public function preload(#[Language('JavaScript')] string $code, bool $permanent = false): LoadedScript
    {
        $handle = $this->app->saucer->saucer_script_new($code, ScriptTime::SAUCER_SCRIPT_TIME_CREATION);

        if ($permanent) {
            $this->app->saucer->saucer_script_set_permanent($handle, true);
        }

        return $this->registerAndInject(new LoadedScript(
            api: $this->app->saucer,
            id: LoadedScriptId::fromScriptHandle($this->app->saucer, $handle),
            code: $code,
            isPermanent: $permanent,
            time: LoadedScriptLoadingTime::OnCreated,
        ));
    }

    public function add(#[Language('JavaScript')] string $code): LoadedScript
    {
        $handle = $this->app->saucer->saucer_script_new($code, ScriptTime::SAUCER_SCRIPT_TIME_READY);

        return $this->registerAndInject(new LoadedScript(
            api: $this->app->saucer,
            id: LoadedScriptId::fromScriptHandle($this->app->saucer, $handle),
            code: $code,
            isPermanent: false,
            time: LoadedScriptLoadingTime::OnReady,
        ));
    }

    private function registerAndInject(LoadedScript $script): LoadedScript
    {
        $this->scripts[$script] = null;

        $this->app->saucer->saucer_webview_inject($this->ptr, $script->id->ptr);

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
        $this->app->saucer->saucer_webview_clear_scripts($this->ptr);
    }
}
