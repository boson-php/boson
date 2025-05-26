<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Vendor;

use Boson\Component\OsInfo\FamilyInterface;

/**
 * Returns general (and imprecise) OS information
 */
final class GenericVendorFactory implements VendorFactoryInterface
{
    public function createVendor(FamilyInterface $family): VendorInfo
    {
        $name = \php_uname('s');

        if ($name === '') {
            $name = 'Generic OS';
        }

        $version = \php_uname('r');

        if ($version === '') {
            $version = '0.0.0';
        }

        return new VendorInfo(
            name: $name,
            version: self::parseVersion($version) ?? $version,
        );
    }

    /**
     * @return non-empty-string|null
     */
    public static function parseVersion(string $version): ?string
    {
        \preg_match('/^\d+(?:\.\d+){0,3}/', $version, $matches);

        /** @var non-empty-string|null */
        return $matches[0] ?? null;
    }
}
