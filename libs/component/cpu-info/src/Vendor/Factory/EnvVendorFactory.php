<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Vendor\Factory;

use Boson\Component\CpuInfo\Vendor\VendorInfo;

final readonly class EnvVendorFactory implements VendorFactoryInterface
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_BUILTIN_NAME_ENV_NAME = 'PROCESSOR_IDENTIFIER';

    /**
     * @var non-empty-string
     */
    public const string DEFAULT_BUILTIN_CORES_ENV_NAME = 'NUMBER_OF_PROCESSORS';

    public function __construct(
        private VendorFactoryInterface $delegate,
        /**
         * @var list<non-empty-string>
         */
        private array $envNameVariableNames = [],
        /**
         * @var list<non-empty-string>
         */
        private array $envLogicalCoresVariableNames = [],
    ) {}

    public static function createForBuiltinEnvVariables(VendorFactoryInterface $delegate): self
    {
        return new self($delegate, [
            self::DEFAULT_BUILTIN_NAME_ENV_NAME,
        ], [
            self::DEFAULT_BUILTIN_CORES_ENV_NAME,
        ]);
    }

    /**
     * @return non-empty-string|null
     */
    private function tryGetNameFromEnvironment(): ?string
    {
        foreach ($this->envNameVariableNames as $name) {
            $server = $_SERVER[$name] ?? null;

            if (\is_string($server) && $server !== '') {
                return $server;
            }
        }

        return null;
    }

    /**
     * @return non-empty-string|null
     */
    private function tryGetLogicalCoresFromEnvironment(): ?string
    {
        foreach ($this->envLogicalCoresVariableNames as $name) {
            $server = $_SERVER[$name] ?? null;

            if (\is_string($server) && $server !== '') {
                return $server;
            }
        }

        return null;
    }

    public function createVendor(): VendorInfo
    {
        $fallback = $this->delegate->createVendor();

        $name = $this->tryGetNameFromEnvironment();

        if ($name === null) {
            return $fallback;
        }

        $cores = $this->tryGetLogicalCoresFromEnvironment();

        $physicalCores = $fallback->physicalCores;
        $logicalCores = $fallback->logicalCores;

        if (\is_numeric($cores)) {
            $physicalCores = $logicalCores = \max(1, (int) $cores);
        }

        return new VendorInfo(
            name: $name,
            vendor: $fallback->vendor,
            physicalCores: $physicalCores,
            logicalCores: $logicalCores,
        );
    }
}
