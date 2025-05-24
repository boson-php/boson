<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Bindings;

/**
 * @template-extends \Traversable<non-empty-string, \Closure(mixed...):(void|mixed)>
 */
interface BindingsMapInterface extends \Traversable, \Countable
{
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
