<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Vendor\Factory;

use Boson\Component\CpuInfo\Vendor\VendorInfo;

final readonly class DefaultVendorFactory implements VendorFactoryInterface
{
    private VendorFactoryInterface $default;

    public function __construct()
    {
        $this->default = new EnvVendorFactory(
            delegate: new GenericVendorFactory(),
        );
    }

    public function createVendor(): VendorInfo
    {
        return $this->default->createVendor();
    }
}
