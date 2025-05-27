<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Vendor\Factory;

use Boson\Component\CpuInfo\Vendor\VendorInfo;

interface VendorFactoryInterface
{
    public function createVendor(): VendorInfo;
}
