<?php

declare(strict_types=1);

namespace Boson\Component\Ipc;

use Boson\Component\Ipc\Driver\DriverCreateInfo;

final readonly class ChannelCreateInfo
{
    public function __construct(
        public DriverCreateInfo $driver = new DriverCreateInfo(),
    ) {}
}
