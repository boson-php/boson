<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory;

use Boson\Component\OsInfo\Family\Factory\DefaultFamilyFactory;
use Boson\Component\OsInfo\Family\Factory\FamilyFactoryInterface;
use Boson\Component\OsInfo\OperatingSystem;
use Boson\Component\OsInfo\Standard\Factory\DefaultStandardsFactory;
use Boson\Component\OsInfo\Standard\Factory\StandardsFactoryInterface;
use Boson\Component\OsInfo\Vendor\Factory\DefaultVendorFactory;
use Boson\Component\OsInfo\Vendor\Factory\VendorFactoryInterface;

final readonly class GenericOperatingSystemFactory implements OperatingSystemFactoryInterface
{
    public function __construct(
        private FamilyFactoryInterface $familyFactory = new DefaultFamilyFactory(),
        private VendorFactoryInterface $vendorFactory = new DefaultVendorFactory(),
        private StandardsFactoryInterface $standardsFactory = new DefaultStandardsFactory(),
    ) {}

    public function createOperatingSystem(): OperatingSystem
    {
        $family = $this->familyFactory->createFamily();
        $vendor = $this->vendorFactory->createVendor($family);
        $standards = $this->standardsFactory->createStandards($family);

        return new OperatingSystem(
            family: $family,
            name: $vendor->name,
            version: $vendor->version,
            codename: $vendor->codename,
            edition: $vendor->edition,
            standards: $standards,
        );
    }
}
