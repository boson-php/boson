<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Vendor;

use Boson\Component\OsInfo\FamilyInterface;

interface VendorFactoryInterface
{
    public function createVendor(FamilyInterface $family): VendorInfo;
}
