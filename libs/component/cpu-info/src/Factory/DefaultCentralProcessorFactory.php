<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Factory;

use Boson\Component\CpuInfo\Architecture\Factory\ArchitectureFactoryInterface;
use Boson\Component\CpuInfo\Architecture\Factory\DefaultArchitectureFactory;
use Boson\Component\CpuInfo\CentralProcessor;
use Boson\Component\CpuInfo\Vendor\Factory\DefaultVendorFactory;
use Boson\Component\CpuInfo\Vendor\Factory\VendorFactoryInterface;

final readonly class DefaultCentralProcessorFactory implements CentralProcessorFactoryInterface
{
    public function __construct(
        private ArchitectureFactoryInterface $architectureFactory = new DefaultArchitectureFactory(),
        private VendorFactoryInterface $vendorFactory = new DefaultVendorFactory(),
    ) {}

    public function createCentralProcessor(): CentralProcessor
    {
        $arch = $this->architectureFactory->createArchitecture();
        $vendor = $this->vendorFactory->createVendor();

        return new CentralProcessor(
            arch: $arch,
            name: $vendor->name,
        );
    }
}
