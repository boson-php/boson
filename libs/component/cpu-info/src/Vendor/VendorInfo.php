<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Vendor;

readonly class VendorInfo implements \Stringable
{
    public function __construct(
        /**
         * Gets current CPU generic name
         *
         * @var non-empty-string
         */
        public string $name,
    ) {}

    public function __toString(): string
    {
        return $this->name;
    }
}
