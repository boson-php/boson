<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Vendor\Factory;

use Boson\Component\OsInfo\FamilyInterface;
use Boson\Component\OsInfo\Vendor\VendorInfo;

final readonly class DefaultVendorFactory implements VendorFactoryInterface
{
    private VendorFactoryInterface $default;

    public function __construct()
    {
        $this->default = new LinuxOSReleaseVendorFactory(
            delegate: new Win32RegistryVendorFactory(
                delegate: new Win32WmiVendorFactory(
                    delegate: new Win32GenericVendorFactory(
                        delegate: new GenericVendorFactory(),
                    )
                )
            )
        );
    }

    public function createVendor(FamilyInterface $family): VendorInfo
    {
        return $this->default->createVendor($family);
    }
}
