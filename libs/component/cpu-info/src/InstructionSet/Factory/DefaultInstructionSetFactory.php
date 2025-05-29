<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet\Factory;

use Boson\Component\CpuInfo\ArchitectureInterface;
use Boson\Component\OsInfo\Family\Factory\FamilyFactoryInterface;

final class DefaultInstructionSetFactory implements InstructionSetFactoryInterface
{
    private InstructionSetFactoryInterface $default;

    public function __construct(?FamilyFactoryInterface $osFamilyFactory = null)
    {
        $this->default = new LinuxProcCpuInfoInstructionSetFactory(
            delegate: new CpuIdInstructionSetFactory(
                delegate: new GenericInstructionSetFactory(),
                osFamilyFactory: $osFamilyFactory,
            ),
            osFamilyFactory: $osFamilyFactory,
        );
    }

    public function createInstructionSets(ArchitectureInterface $arch): array
    {
        return $this->default->createInstructionSets($arch);
    }
}
