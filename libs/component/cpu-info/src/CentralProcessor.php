<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo;

use Boson\Component\CpuInfo\Factory\DefaultCentralProcessorFactory;
use Boson\Component\CpuInfo\Factory\InMemoryCentralProcessorFactory;
use Boson\Component\CpuInfo\Vendor\VendorInfo;

final readonly class CentralProcessor extends VendorInfo
{
    /**
     * @var list<InstructionSetInterface>
     */
    public array $instructionSets;

    /**
     * @param non-empty-string $name
     * @param non-empty-string|null $vendor
     * @param int<1, max> $physicalCores
     * @param int<1, max> $logicalCores
     * @param iterable<mixed, InstructionSetInterface> $instructionSets
     */
    public function __construct(
        /**
         * Gets current CPU architecture type
         */
        public ArchitectureInterface $arch,
        string $name,
        ?string $vendor = null,
        int $physicalCores = 1,
        int $logicalCores = 1,
        iterable $instructionSets = [],
    ) {
        $this->instructionSets = \iterator_to_array($instructionSets, false);

        parent::__construct(
            name: $name,
            vendor: $vendor,
            physicalCores: $physicalCores,
            logicalCores: $logicalCores,
        );
    }

    /**
     * @api
     */
    public static function createFromGlobals(): CentralProcessor
    {
        /** @phpstan-var InMemoryCentralProcessorFactory $factory */
        static $factory = new InMemoryCentralProcessorFactory(
            delegate: new DefaultCentralProcessorFactory(),
        );

        return $factory->createCentralProcessor();
    }
}
