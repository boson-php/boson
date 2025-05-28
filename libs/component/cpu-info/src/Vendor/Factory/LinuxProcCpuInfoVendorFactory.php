<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Vendor\Factory;

use Boson\Component\CpuInfo\Internal\LinuxProcCpuInfo;
use Boson\Component\CpuInfo\Vendor\VendorInfo;
use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\Family\Factory\FamilyFactoryInterface as OsFamilyFactoryInterface;

final readonly class LinuxProcCpuInfoVendorFactory implements VendorFactoryInterface
{
    public function __construct(
        private VendorFactoryInterface $delegate,
        private ?OsFamilyFactoryInterface $osFamilyFactory = null,
    ) {}

    public function createVendor(): VendorInfo
    {
        $family = $this->osFamilyFactory?->createFamily()
            ?? Family::createFromGlobals();

        $fallback = $this->delegate->createVendor();

        if (!$family->is(Family::Linux) || !LinuxProcCpuInfo::isReadable()) {
            return $fallback;
        }

        return $this->tryCreateFromProcCpuInfo($fallback);
    }

    private function tryCreateFromProcCpuInfo(VendorInfo $fallback): VendorInfo
    {
        $processors = new LinuxProcCpuInfo()
            ->getSegmentsByPhysicalId();

        $name = $this->getProcessorName($processors);

        if ($name === null || $name === '') {
            return $fallback;
        }

        return new VendorInfo(
            name: $name,
            vendor: $this->getProcessorVendor($processors)
                ?? $fallback->vendor,
            physicalCores: $this->getProcessorPhysicalCores($processors)
                ?? $fallback->physicalCores,
            logicalCores: $this->getProcessorLogicalCores($processors)
                ?? $fallback->logicalCores,
        );
    }

    /**
     * @param array<numeric-string|int, list<array<non-empty-string, string>>> $processors
     *
     * @return int<1, max>|null
     */
    private function getProcessorPhysicalCores(array $processors): ?int
    {
        foreach ($processors as $cores) {
            return \max(1, \count($cores));
        }

        return null;
    }

    /**
     * @param array<numeric-string|int, list<array<non-empty-string, string>>> $processors
     *
     * @return int<1, max>|null
     */
    private function getProcessorLogicalCores(array $processors): ?int
    {
        $cores = 0;

        foreach ($processors as $processor) {
            foreach ($processor as $core) {
                $cores += \max(1, (int) ($core['cpu cores'] ?? 0));
            }
        }

        return $cores === 0 ? null : $cores;
    }

    /**
     * @param array<numeric-string|int, list<array<non-empty-string, string>>> $processors
     *
     * @return non-empty-string|null
     */
    private function getProcessorName(array $processors): ?string
    {
        foreach ($processors as $processor) {
            foreach ($processor as $core) {
                $name = $core['model name'] ?? null;

                if ($name !== null && $name !== '') {
                    return $name;
                }
            }
        }

        return null;
    }

    /**
     * @param array<numeric-string|int, list<array<non-empty-string, string>>> $processors
     *
     * @return non-empty-string|null
     */
    private function getProcessorVendor(array $processors): ?string
    {
        foreach ($processors as $processor) {
            foreach ($processor as $core) {
                $vendor = $core['vendor_id'] ?? null;

                if ($vendor !== null && $vendor !== '') {
                    return $vendor;
                }
            }
        }

        return null;
    }
}
