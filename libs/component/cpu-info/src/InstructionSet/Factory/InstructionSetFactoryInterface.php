<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet\Factory;

use Boson\Component\CpuInfo\ArchitectureInterface;
use Boson\Component\CpuInfo\InstructionSetInterface;

interface InstructionSetFactoryInterface
{
    /**
     * @return list<InstructionSetInterface>
     */
    public function createInstructionSets(ArchitectureInterface $arch): array;
}
