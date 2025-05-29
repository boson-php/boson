<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Vendor\Factory;

use Boson\Component\CpuInfo\Vendor\VendorInfo;
use Boson\Component\OsInfo\Family\Factory\FamilyFactoryInterface as OsFamilyFactoryInterface;

final readonly class DefaultVendorFactory implements VendorFactoryInterface
{
    private VendorFactoryInterface $default;

    public function __construct(?OsFamilyFactoryInterface $osFamilyFactory = null)
    {
        $this->default = new LinuxProcCpuInfoVendorFactory(
            delegate: new Win32WmiVendorFactory(
                delegate: EnvVendorFactory::createForBuiltinEnvVariables(
                    delegate: new GenericVendorFactory(),
                ),
                osFamilyFactory: $osFamilyFactory,
            ),
            osFamilyFactory: $osFamilyFactory,
        );
    }

    public function createVendor(): VendorInfo
    {
        return $this->default->createVendor();
    }
}
