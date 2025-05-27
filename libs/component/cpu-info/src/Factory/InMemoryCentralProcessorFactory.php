<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory;

use Boson\Component\CpuInfo\CentralProcessor;

final class InMemoryCentralProcessorFactory implements CentralProcessorFactoryInterface
{
    private ?CentralProcessor $current = null;

    public function __construct(
        private readonly CentralProcessorFactoryInterface $delegate,
    ) {}

    public function createCentralProcessor(): CentralProcessor
    {
        return $this->current ??= $this->delegate->createCentralProcessor();
    }
}
