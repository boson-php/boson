<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Component;

interface HasEventListenersInterface
{
    /**
     * @param non-empty-string $event
     * @param array<array-key, mixed> $args
     */
    public function onEventFired(string $event, array $args = []): void;

    /**
     * Must return an array containing the names of all event listeners and
     * its arguments.
     *
     * ```
     * return [
     *      'click' => [
     *          'x' => 'clientX',
     *          'y' => 'clientY',
     *          'pointerType'
     *      ],
     * ];
     * ```
     *
     * @return array<non-empty-string, array<array-key, mixed>>
     */
    public static function getEventListeners(): array;
}
