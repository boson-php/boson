<?php

declare(strict_types=1);

namespace Boson\Internal\DeferRunner;

interface DeferRunnerInterface
{
    /**
     * Indicates whether the runner is supported on the current platform.
     */
    public bool $isSupported { get; }

    /**
     * Registers a callback to be executed when the script execution ends.
     *
     * @param callable(): void $callback The callback to be executed
     */
    public function register(callable $callback): void;
}
