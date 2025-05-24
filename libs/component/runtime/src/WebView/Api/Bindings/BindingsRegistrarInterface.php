<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Bindings;

use Boson\WebView\Api\Bindings\Exception\FunctionAlreadyDefinedException;

interface BindingsRegistrarInterface
{
    /**
     * Binds a PHP callback to a new global JavaScript function.
     *
     * This method creates a JavaScript function that can be called from the
     * webview, which will execute the provided PHP callback. The function can
     * be registered in nested namespaces using dot notation
     * (e.g., "app.functions.myFunction").
     *
     * @param non-empty-string $function The name of the JavaScript function
     * @param \Closure(mixed...):(void|mixed) $callback The PHP callback to execute
     *
     * @throws FunctionAlreadyDefinedException if the function is already defined
     */
    public function bind(string $function, \Closure $callback): void;
}
