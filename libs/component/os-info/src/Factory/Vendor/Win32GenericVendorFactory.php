<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Vendor;

use Boson\Component\OsInfo\FamilyInterface;

final readonly class Win32GenericVendorFactory implements VendorFactoryInterface
{
    public function __construct(
        private VendorFactoryInterface $delegate,
    ) {}

    public function createVendor(FamilyInterface $family): VendorInfo
    {
        $parent = $this->delegate->createVendor($family);

        $major = self::getConstantValue('PHP_WINDOWS_VERSION_MAJOR');
        if ($major === null) {
            return $parent;
        }

        return new VendorInfo(
            name: $parent->name,
            version: \vsprintf('%d.%d.%d', [
                $major,
                self::getConstantValue('PHP_WINDOWS_VERSION_MINOR') ?? 0,
                self::getConstantValue('PHP_WINDOWS_VERSION_BUILD') ?? 0,
            ]),
        );
    }

    /**
     * @param non-empty-string $name
     */
    private static function getConstantValue(string $name): ?int
    {
        if (!\defined($name)) {
            return null;
        }

        $value = \constant($name);

        if (\is_int($value)) {
            return $value;
        }

        return null;
    }
}
