<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\WebView\Api\Bindings\Exception\FunctionAlreadyDefinedException;

/**
 * Manages the binding between PHP callbacks and JavaScript functions.
 *
 * Provides functionality to create and manage JavaScript functions
 * that are bound to PHP callbacks. It handles the registration, execution,
 * and cleanup of these bindings, as well as the communication between
 * JavaScript and PHP through a message-based RPC system.
 *
 * @template-extends \Traversable<non-empty-string, \Closure(mixed...):mixed>
 */
interface BindingsApiInterface extends \Traversable, \Countable
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
     * @param \Closure(mixed...):mixed $callback The PHP callback to execute
     *
     * @throws FunctionAlreadyDefinedException if the function is already defined
     */
    public function bind(string $function, \Closure $callback): void;

    /**
     * Returns {@see true} in case of passed function is bound.
     *
     * @param non-empty-string $function
     */
    public function isBound(string $function): bool;

    /**
     * Gets the count of registered functions.
     *
     * @return int<0, max> The number of registered functions
     */
    public function count(): int;
}
