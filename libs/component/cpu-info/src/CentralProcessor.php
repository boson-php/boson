<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo;

use Boson\Component\CpuInfo\Factory\DefaultCentralProcessorFactory;
use Boson\Component\CpuInfo\Factory\InMemoryCentralProcessorFactory;
use Boson\Component\CpuInfo\Vendor\VendorInfo;

final readonly class CentralProcessor extends VendorInfo
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        /**
         * Gets current CPU architecture type
         */
        public ArchitectureInterface $arch,
        string $name,
    ) {
        parent::__construct(
            name: $name,
        );
    }

    /**
     * @api
     */
    public static function createFromGlobals(): CentralProcessor
    {
        static $factory = new InMemoryCentralProcessorFactory(
            delegate: new DefaultCentralProcessorFactory(),
        );

        return $factory->createCentralProcessor();
    }
}
