<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory;

use Boson\Component\CpuInfo\CentralProcessor;

final readonly class DefaultCentralProcessorFactory implements CentralProcessorFactoryInterface
{
    private CentralProcessorFactoryInterface $default;

    public function __construct()
    {
        $this->default = new GenericCentralProcessorFactory();
    }

    public function createCentralProcessor(): CentralProcessor
    {
        return $this->default->createCentralProcessor();
    }
}
