<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Vendor;

final readonly class VendorInfo
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public string $name,
        /**
         * @var non-empty-string
         */
        public string $version,
        /**
         * @var non-empty-string|null
         */
        public ?string $codename = null,
        /**
         * @var non-empty-string|null
         */
        public ?string $edition = null,
    ) {}
}
