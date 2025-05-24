<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Component;

interface HasMethodsInterface
{
    /**
     * Called when html element method has been called.
     *
     * @param non-empty-string $method
     * @param array<array-key, mixed> $args
     */
    public function onMethodCalled(string $method, array $args = []): mixed;

    /**
     * Must return an array containing the names of all methods for which
     * the element requires call notifications.
     *
     * ```
     * return ['methodName', 'otherMethodName'];
     * ```
     *
     * @return list<non-empty-string>
     */
    public static function getMethodNames(): array;
}
