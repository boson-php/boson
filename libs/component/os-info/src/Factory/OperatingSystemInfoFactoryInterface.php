<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory;

use Boson\Component\OsInfo\OperatingSystemInfo;

interface OperatingSystemInfoFactoryInterface
{
    public function createOperatingSystem(): OperatingSystemInfo;
}
