<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory;

use Boson\Component\CpuInfo\Architecture\Factory\ArchitectureFactoryInterface;
use Boson\Component\CpuInfo\Architecture\Factory\DefaultArchitectureFactory;
use Boson\Component\CpuInfo\CentralProcessor;
use Boson\Component\CpuInfo\InstructionSet\Factory\DefaultInstructionSetFactory;
use Boson\Component\CpuInfo\InstructionSet\Factory\InstructionSetFactoryInterface;
use Boson\Component\CpuInfo\Vendor\Factory\DefaultVendorFactory;
use Boson\Component\CpuInfo\Vendor\Factory\VendorFactoryInterface;

final readonly class GenericCentralProcessorFactory implements CentralProcessorFactoryInterface
{
    public function __construct(
        private VendorFactoryInterface $vendorFactory = new DefaultVendorFactory(),
        private ArchitectureFactoryInterface $architectureFactory = new DefaultArchitectureFactory(),
        private InstructionSetFactoryInterface $instructionSetFactory = new DefaultInstructionSetFactory(),
    ) {}

    public function createCentralProcessor(): CentralProcessor
    {
        $arch = $this->architectureFactory->createArchitecture();
        $vendor = $this->vendorFactory->createVendor();
        $instructionSets = $this->instructionSetFactory->createInstructionSets($arch);

        return new CentralProcessor(
            arch: $arch,
            name: $vendor->name,
            vendor: $vendor->vendor,
            physicalCores: $vendor->physicalCores,
            logicalCores: $vendor->logicalCores,
            instructionSets: $instructionSets,
        );
    }
}
