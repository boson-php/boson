<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory;

use Boson\Component\CpuInfo\CentralProcessor;

interface CentralProcessorFactoryInterface
{
    public function createCentralProcessor(): CentralProcessor;
}
