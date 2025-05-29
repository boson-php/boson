<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Vendor;

readonly class VendorInfo implements \Stringable
{
    public function __construct(
        /**
         * Gets current CPU name
         *
         * @var non-empty-string
         */
        public string $name,
        /**
         * Gets current CPU generic vendor name
         *
         * @var non-empty-string|null
         */
        public ?string $vendor = null,
        /**
         * @var int<1, max>
         */
        public int $physicalCores = 1,
        /**
         * @var int<1, max>
         */
        public int $logicalCores = 1,
    ) {}

    public function __toString(): string
    {
        return $this->name;
    }
}
