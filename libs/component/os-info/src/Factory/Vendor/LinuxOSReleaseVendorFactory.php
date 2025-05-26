<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Vendor;

use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\FamilyInterface;

final readonly class LinuxOSReleaseVendorFactory implements VendorFactoryInterface
{
    public function __construct(
        private VendorFactoryInterface $delegate,
    ) {}

    public function createVendor(FamilyInterface $family): VendorInfo
    {
        if (!$family->is(Family::Unix) || !\is_readable('/etc/os-release')) {
            return $this->delegate->createVendor($family);
        }

        /** @var array<non-empty-string, string> $info */
        $info = (array) @\parse_ini_file('/etc/os-release');

        $name = $this->fetchName($info);
        $version = $this->fetchVersion($info);

        if ($name === null || $version === null) {
            $previous = $this->delegate->createVendor($family);

            $name ??= $previous->name;
            $version ??= $previous->version;
        }

        return new VendorInfo($name, $version, $this->fetchCodename($info));
    }

    /**
     * @param array<non-empty-string, string> $info
     *
     * @return non-empty-string|null
     */
    private function fetchCodename(array $info): ?string
    {
        $codename = $this->fetchCodenameFromVersion($info);

        $codename ??= $info['VERSION_CODENAME'] ?? '';

        return $codename === '' ? null : $codename;
    }

    /**
     * @param array<non-empty-string, string> $info
     */
    private function fetchCodenameFromVersion(array $info): ?string
    {
        $version = $info['VERSION'] ?? '';

        if ($version === '') {
            return null;
        }

        \preg_match('/\((.+?)\)$/u', $version, $matches);

        return $matches[1] ?? null;
    }

    /**
     * @param array<non-empty-string, string> $info
     *
     * @return non-empty-string|null
     */
    private function fetchVersion(array $info): ?string
    {
        $version = GenericVendorFactory::parseVersion($info['VERSION'] ?? '');

        if ($version !== null && $version !== '') {
            return $version;
        }

        $version = $info['VERSION_ID'] ?? '';

        return $version === '' ? null : $version;
    }

    /**
     * @param array<non-empty-string, string> $info
     *
     * @return non-empty-string|null
     */
    private function fetchName(array $info): ?string
    {
        $name = $info['NAME'] ?? '';

        return $name === '' ? null : $name;
    }
}
