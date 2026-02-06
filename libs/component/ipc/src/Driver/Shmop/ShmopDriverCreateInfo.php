<?php

declare(strict_types=1);

namespace Boson\Component\Ipc\Driver\Shmop;

final readonly class ShmopDriverCreateInfo
{
    public function __construct(
        public int $memoryId,
        /**
         * @var int<0, max>
         */
        public int $memoryInitialSize,
    ) {}
}
