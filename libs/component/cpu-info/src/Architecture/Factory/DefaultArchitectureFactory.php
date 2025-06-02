<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Architecture\Factory;

use Boson\Component\CpuInfo\ArchitectureInterface;

final readonly class DefaultArchitectureFactory implements ArchitectureFactoryInterface
{
    private ArchitectureFactoryInterface $default;

    public function __construct()
    {
        $this->default = new EnvArchitectureFactory(
            delegate: new GenericArchitectureFactory(),
        );
    }

    public function createArchitecture(): ArchitectureInterface
    {
        return $this->default->createArchitecture();
    }
}
