<?php

declare(strict_types=1);

namespace Boson\Internal\QuitHandler;

interface QuitHandlerInterface
{
    /**
     * Get handler supported status.
     *
     * Contains {@see true} in case of handler is
     * supported or {@see false} instead.
     */
    public bool $isSupported { get; }

    /**
     * Register quit handler.
     *
     * @param callable():void $then
     */
    public function register(callable $then): void;
}
