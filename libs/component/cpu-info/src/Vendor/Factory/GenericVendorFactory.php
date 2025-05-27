<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Vendor\Factory;

use Boson\Component\CpuInfo\Vendor\VendorInfo;

/**
 * Returns general (and imprecise) CPU information
 */
final readonly class GenericVendorFactory implements VendorFactoryInterface
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_CPU_NAME = 'Generic CPU';

    public function __construct(
        private VendorInfo $default = new VendorInfo(
            name: self::DEFAULT_CPU_NAME,
        ),
    ) {}

    public function createVendor(): VendorInfo
    {
        dd($_SERVER);

        $name = \php_uname('m');

        if ($name === '') {
            $name = $this->default->name;
        }

        return new VendorInfo(
            name: $name,
        );
    }
}
