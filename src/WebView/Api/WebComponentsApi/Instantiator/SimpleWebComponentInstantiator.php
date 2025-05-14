<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Instantiator;

use Boson\WebView\Api\WebComponentsApi\ReactiveElementContext;

final readonly class SimpleWebComponentInstantiator implements WebComponentInstantiatorInterface
{
    /**
     * @var (\Closure(ReactiveElementContext<object>):object)|null
     */
    private ?\Closure $callback;

    /**
     * @param (callable(ReactiveElementContext<object>):object)|null $callback
     */
    public function __construct(
        ?callable $callback = null,
    ) {
        $this->callback = $callback === null ? null : $callback(...);
    }

    public function create(ReactiveElementContext $context): object
    {
        $class = $context->component;

        if ($this->callback !== null) {
            return ($this->callback)($context);
        }

        return new $class($context);
    }
}
