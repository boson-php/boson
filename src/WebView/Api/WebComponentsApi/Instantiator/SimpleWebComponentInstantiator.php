<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Instantiator;

use Boson\WebView\Api\WebComponentsApi\ReactiveElementContext;
use Boson\WebView\WebView;

final readonly class SimpleWebComponentInstantiator implements WebComponentInstantiatorInterface
{
    /**
     * @var (\Closure(WebView, ReactiveElementContext<object>):object)|null
     */
    private ?\Closure $callback;

    /**
     * @param (callable(WebView, ReactiveElementContext<object>):object)|null $callback
     */
    public function __construct(
        ?callable $callback = null,
    ) {
        $this->callback = $callback === null ? null : $callback(...);
    }

    public function create(WebView $webview, ReactiveElementContext $context): object
    {
        $class = $context->component;

        if ($this->callback !== null) {
            return ($this->callback)($webview, $context);
        }

        return new $class($webview, $context);
    }
}
