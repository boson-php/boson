<?php

declare(strict_types=1);

namespace Boson\Component\Ipc\Mutex;

interface MutexInterface
{
    public bool $isAcquired {
        get;
    }

    public function acquire(): void;

    public function release(): void;
}
