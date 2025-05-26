<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory;

use Boson\Component\OsInfo\Factory\Family\EnvFamilyFactory;
use Boson\Component\OsInfo\Factory\Family\FamilyFactoryInterface;
use Boson\Component\OsInfo\Factory\Family\GenericFamilyFactory;
use Boson\Component\OsInfo\Factory\Standards\GenericStandardsFactory;
use Boson\Component\OsInfo\Factory\Standards\StandardsFactoryInterface;
use Boson\Component\OsInfo\Factory\Vendor\GenericVendorFactory;
use Boson\Component\OsInfo\Factory\Vendor\LinuxOSReleaseVendorFactory;
use Boson\Component\OsInfo\Factory\Vendor\VendorFactoryInterface;
use Boson\Component\OsInfo\Factory\Vendor\Win32GenericVendorFactory;
use Boson\Component\OsInfo\Factory\Vendor\Win32RegistryVendorFactory;
use Boson\Component\OsInfo\Factory\Vendor\Win32WmiVendorFactory;
use Boson\Component\OsInfo\OperatingSystemInfo;

final readonly class OperatingSystemInfoFactory implements OperatingSystemInfoFactoryInterface
{
    private FamilyFactoryInterface $familyFactory;
    private StandardsFactoryInterface $standardsFactory;
    private VendorFactoryInterface $vendorFactory;

    public function __construct(
        ?FamilyFactoryInterface $familyFactory = null,
        ?StandardsFactoryInterface $standardsFactory = null,
        ?VendorFactoryInterface $vendorFactory = null,
    ) {
        $this->familyFactory = $familyFactory ?? $this->createFamilyFactory();
        $this->standardsFactory = $standardsFactory ?? $this->createStandardsFactory();
        $this->vendorFactory = $vendorFactory ?? $this->createVendorFactory();
    }

    private function createFamilyFactory(): FamilyFactoryInterface
    {
        return new EnvFamilyFactory(new GenericFamilyFactory());
    }

    private function createStandardsFactory(): StandardsFactoryInterface
    {
        return new GenericStandardsFactory();
    }

    private function createVendorFactory(): VendorFactoryInterface
    {
        $factory = new GenericVendorFactory();
        $factory = new Win32GenericVendorFactory($factory);
        $factory = new Win32WmiVendorFactory($factory);
        $factory = new Win32RegistryVendorFactory($factory);
        $factory = new LinuxOSReleaseVendorFactory($factory);

        return $factory;
    }

    public function createOperatingSystem(): OperatingSystemInfo
    {
        $family = $this->familyFactory->createFamily();
        $vendor = $this->vendorFactory->createVendor($family);
        $standards = $this->standardsFactory->createStandards($family);

        return new OperatingSystemInfo(
            name: $vendor->name,
            version: $vendor->version,
            family: $family,
            codename: $vendor->codename,
            edition: $vendor->edition,
            standards: $standards,
        );
    }
}
