<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Vendor\Factory;

use Boson\Component\CpuInfo\Vendor\VendorInfo;

final readonly class EnvVendorFactory implements VendorFactoryInterface
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_ENV_NAME = 'PROCESSOR_IDENTIFIER';

    public function __construct(
        private VendorFactoryInterface $delegate,
        /**
         * @var list<non-empty-string>
         */
        private array $envVariableNames = [
            self::DEFAULT_ENV_NAME,
        ],
    ) {}

    /**
     * @return non-empty-string|null
     */
    private function tryGetNameFromEnvironment(): ?string
    {
        foreach ($this->envVariableNames as $name) {
            $server = $_SERVER[$name] ?? null;

            if (\is_string($server) && $server !== '') {
                return $server;
            }
        }

        return null;
    }

    public function createVendor(): VendorInfo
    {
        $name = $this->tryGetNameFromEnvironment();

        if ($name === null) {
            return $this->delegate->createVendor();
        }

        return new VendorInfo(
            name: $name,
        );
    }
}
