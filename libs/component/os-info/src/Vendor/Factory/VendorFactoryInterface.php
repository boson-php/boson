<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Vendor\Factory;

use Boson\Component\OsInfo\FamilyInterface;
use Boson\Component\OsInfo\Vendor\VendorInfo;

interface VendorFactoryInterface
{
    public function createVendor(FamilyInterface $family): VendorInfo;
}
