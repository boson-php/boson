<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet\Factory;

use Boson\Component\CpuInfo\ArchitectureInterface;

final class GenericInstructionSetFactory implements InstructionSetFactoryInterface
{
    public function createInstructionSets(ArchitectureInterface $arch): array
    {
        return [];
    }
}
