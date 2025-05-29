<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Vendor\Factory;

use Boson\Component\CpuInfo\Vendor\VendorInfo;
use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\Family\Factory\FamilyFactoryInterface as OsFamilyFactoryInterface;
use COM as ComExtension;

final readonly class Win32WmiVendorFactory implements VendorFactoryInterface
{
    /**
     * @var non-empty-string
     */
    private const string WMI_MODULE_NAME = 'winmgmts:{impersonationLevel=impersonate}//./root/cimv2';

    public function __construct(
        private VendorFactoryInterface $delegate,
        private ?OsFamilyFactoryInterface $osFamilyFactory = null,
    ) {}

    public function createVendor(): VendorInfo
    {
        $family = $this->osFamilyFactory?->createFamily()
            ?? Family::createFromGlobals();

        $fallback = $this->delegate->createVendor();

        if (!$family->is(Family::Windows) || !\class_exists(ComExtension::class)) {
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
        $wmi = new ComExtension(self::WMI_MODULE_NAME, null, \CP_UTF8);

        /** @phpstan-ignore-next-line : ExecQuery is defined */
        $processors = $wmi->ExecQuery(<<<'SQL'
            SELECT Name, Manufacturer, NumberOfCores, NumberOfLogicalProcessors
            FROM Win32_Processor
            SQL);

        /**
         * @var list<object{
         *     Name: string,
         *     Manufacturer: string,
         *     NumberOfCores: int,
         *     NumberOfLogicalProcessors: int,
         * }> $processors
         */
        foreach ($processors as $processor) {
            return new VendorInfo(
                name: $processor->Name === '' ? $fallback->name : $processor->Name,
                vendor: $processor->Manufacturer === '' ? $fallback->vendor : $processor->Manufacturer,
                physicalCores: \max(1, $processor->NumberOfCores),
                logicalCores: \max(1, $processor->NumberOfLogicalProcessors),
            );
        }

        return $fallback;
    }
}
