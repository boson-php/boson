<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Vendor\Factory;

use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\FamilyInterface;
use Boson\Component\OsInfo\Vendor\VendorInfo;

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
        $fallback = $this->delegate->createVendor($family);

        if (!$family->is(Family::Windows) || !\class_exists(\COM::class)) {
            return $fallback;
        }

        try {
            return $this->tryCreateFromWmi($fallback);
        } catch (\Throwable) {
            return $fallback;
        }
    }

    private function tryCreateFromWmi(VendorInfo $fallback): VendorInfo
    {
        $wmi = new \COM(self::WMI_MODULE_NAME, null, \CP_UTF8);

        /**
         * @var object{
         *     Caption: string,
         *     Version: string
         * } $os
         *
         * @phpstan-ignore-next-line : ExecQuery is defined
         */
        foreach ($wmi->ExecQuery('SELECT Caption, Version FROM Win32_OperatingSystem') as $os) {
            try {
                return new VendorInfo(
                    name: $os->Caption === '' ? $fallback->name : $os->Caption,
                    version: $os->Version === '' ? $fallback->version : $os->Version,
                    codename: $fallback->codename,
                    edition: $fallback->edition,
                );
            } catch (\Throwable) {
                continue;
            }
        }

        return $fallback;
    }
}
