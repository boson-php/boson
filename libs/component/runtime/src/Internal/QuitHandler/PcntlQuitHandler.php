<?php

declare(strict_types=1);

namespace Boson\Internal\QuitHandler;

final readonly class PcntlQuitHandler implements QuitHandlerInterface
{
    public bool $isSupported;

    public function __construct()
    {
        $this->isSupported = \extension_loaded('pcntl');
    }

    public function register(callable $then): void
    {
        if (!$this->isSupported) {
            return;
        }

        \pcntl_async_signals(true);

        foreach ([\SIGINT, \SIGQUIT, \SIGTERM] as $signal) {
            \pcntl_signal($signal, $then(...));
        }
    }
}
