<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory;

use Boson\Component\OsInfo\OperatingSystem;

interface OperatingSystemFactoryInterface
{
    public function createOperatingSystem(): OperatingSystem;
}
