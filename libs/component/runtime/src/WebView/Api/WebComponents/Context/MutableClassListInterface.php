<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context;

interface MutableClassListInterface extends ClassListInterface
{
    /**
     * Adds all class name arguments passed, except those already present.
     *
     * @link https://developer.mozilla.org/docs/Web/API/DOMTokenList/add
     *
     * @param non-empty-string $class
     * @param non-empty-string ...$other
     */
    public function add(string $class, string ...$other): void;

    /**
     * Removes class name arguments passed, if they are present.
     *
     * @link https://developer.mozilla.org/docs/Web/API/DOMTokenList/remove
     *
     * @param non-empty-string $class
     * @param non-empty-string ...$other
     */
    public function remove(string $class, string ...$other): void;

    /**
     * Replaces `$fromClass` clas name with `$toClass`.
     *
     * Returns {@see true} if class name was replaced with `$toClass`,
     * and {@see false} otherwise.
     *
     * @link https://developer.mozilla.org/docs/Web/API/DOMTokenList/replace
     *
     * @param non-empty-string $fromClass
     * @param non-empty-string $toClass
     */
    public function replace(string $fromClass, string $toClass): bool;

    /**
     * "toggles" class name, removing it if it's present and adding
     * it if it's not present.
     *
     * Returns {@see true} if token is now present,
     * and {@see false} otherwise.
     *
     * @link https://developer.mozilla.org/docs/Web/API/DOMTokenList/toggle
     *
     * @param non-empty-string $class
     */
    public function toggle(string $class): bool;
}
