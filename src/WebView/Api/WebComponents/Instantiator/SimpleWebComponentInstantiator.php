<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Instantiator;

use Boson\WebView\Api\WebComponents\Context\ReactiveContext;
use Boson\WebView\WebView;

final readonly class SimpleWebComponentInstantiator implements WebComponentInstantiatorInterface
{
    /**
     * @var (\Closure(WebView, ReactiveContext<object>):object)|null
     */
    private ?\Closure $callback;

    /**
     * @param (callable(WebView, ReactiveContext<object>):object)|null $callback
     */
    public function __construct(
        ?callable $callback = null,
    ) {
        $this->callback = $callback === null ? null : $callback(...);
    }

    public function create(WebView $webview, ReactiveContext $context): object
    {
        $class = $context->component;

        if ($this->callback !== null) {
            return ($this->callback)($webview, $context);
        }

        return new $class($context, $webview);
    }
}
