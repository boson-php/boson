<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Architecture\Factory;

use Boson\Component\CpuInfo\Architecture;
use Boson\Component\CpuInfo\ArchitectureInterface;

final readonly class GenericArchitectureFactory implements ArchitectureFactoryInterface
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_ARCH_NAME = 'Unknown';

    public function __construct(
        /**
         * @var non-empty-string
         */
        private string $default = self::DEFAULT_ARCH_NAME,
    ) {}

    public function createArchitecture(): ArchitectureInterface
    {
        $name = \php_uname('m');

        if ($name === '') {
            $name = $this->default;
        }

        return Architecture::from($name);
    }
}
