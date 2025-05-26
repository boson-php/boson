<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Vendor;

use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\FamilyInterface;

final readonly class Win32WmiVendorFactory implements VendorFactoryInterface
{
    /**
     * @var non-empty-string
     */
    private const string WMI_MODULE_NAME = 'winmgmts:{impersonationLevel=impersonate}//./root/cimv2';

    public function __construct(
        private VendorFactoryInterface $delegate,
    ) {}

    public function createVendor(FamilyInterface $family): VendorInfo
    {
        if (!$family->is(Family::Windows) || !\class_exists(\COM::class)) {
            return $this->delegate->createVendor($family);
        }

        try {
            return $this->tryCreateFromWmi()
                ?? $this->delegate->createVendor($family);
        } catch (\Throwable) {
            return $this->delegate->createVendor($family);
        }
    }

    private function tryCreateFromWmi(): ?VendorInfo
    {
        $wmi = new \COM(self::WMI_MODULE_NAME, null, \CP_UTF8);

        /**
         * @var object{
         *     Caption: non-empty-string,
         *     Version: non-empty-string
         * } $os
         *
         * @phpstan-ignore-next-line : ExecQuery is defined
         */
        foreach ($wmi->ExecQuery('SELECT Caption, Version FROM Win32_OperatingSystem') as $os) {
            try {
                return new VendorInfo($os->Caption, $os->Version);
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }
}
