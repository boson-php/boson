<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Vendor\Factory;

use Boson\Component\OsInfo\FamilyInterface;
use Boson\Component\OsInfo\Vendor\VendorInfo;

/**
 * Returns general (and imprecise) OS information
 */
final readonly class GenericVendorFactory implements VendorFactoryInterface
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_OS_NAME = 'Generic OS';

    /**
     * @var non-empty-string
     */
    public const string DEFAULT_OS_VERSION = '0.0.0';

    public function __construct(
        private VendorInfo $default = new VendorInfo(
            name: self::DEFAULT_OS_NAME,
            version: self::DEFAULT_OS_VERSION,
        ),
    ) {}

    public function createVendor(FamilyInterface $family): VendorInfo
    {
        $name = \php_uname('s');

        if ($name === '') {
            $name = $this->default->name;
        }

        $version = \php_uname('r');

        if ($version === '') {
            $version = $this->default->version;
        }

        return new VendorInfo(
            name: $name,
            version: self::parseVersion($version) ?? $version,
            codename: $this->default->codename,
            edition: $this->default->edition,
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
