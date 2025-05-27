<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Architecture\Factory;

use Boson\Component\CpuInfo\ArchitectureInterface;

final class InMemoryArchitectureFactory implements ArchitectureFactoryInterface
{
    private ?ArchitectureInterface $arch = null;

    public function __construct(
        private readonly ArchitectureFactoryInterface $delegate,
    ) {}

    public function createArchitecture(): ArchitectureInterface
    {
        return $this->arch ??= $this->delegate->createArchitecture();
    }
}
