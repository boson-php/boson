<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo;

use Boson\Component\CpuInfo\Architecture\ArchitectureImpl;
use Boson\Component\CpuInfo\Architecture\Factory\DefaultArchitectureFactory;
use Boson\Component\CpuInfo\Architecture\Factory\InMemoryArchitectureFactory;

final readonly class Architecture implements ArchitectureInterface
{
    use ArchitectureImpl;

    public const ArchitectureInterface x86 = Architecture\X86;
    public const ArchitectureInterface Amd64 = Architecture\AMD64;
    public const ArchitectureInterface Arm = Architecture\ARM;
    public const ArchitectureInterface Arm64 = Architecture\ARM64;
    public const ArchitectureInterface Itanium = Architecture\ITANIUM;
    public const ArchitectureInterface RiscV32 = Architecture\RISCV32;
    public const ArchitectureInterface RiscV64 = Architecture\RISCV64;
    public const ArchitectureInterface Mips = Architecture\MIPS;
    public const ArchitectureInterface Mips64 = Architecture\MIPS64;
    public const ArchitectureInterface PowerPc = Architecture\PPC;
    public const ArchitectureInterface PowerPc64 = Architecture\PPC64;
    public const ArchitectureInterface Sparc = Architecture\SPARC;
    public const ArchitectureInterface Sparc64 = Architecture\SPARC64;

    /**
     * @api
     */
    public static function createFromGlobals(): ArchitectureInterface
    {
        static $factory = new InMemoryArchitectureFactory(
            delegate: new DefaultArchitectureFactory(),
        );

        return $factory->createArchitecture();
    }

    /**
     * @return non-empty-list<ArchitectureInterface>
     */
    public static function cases(): array
    {
        /** @var non-empty-array<non-empty-string, ArchitectureInterface> $cases */
        static $cases = new \ReflectionClass(self::class)
            ->getConstants();

        /** @var non-empty-list<ArchitectureInterface> */
        return \array_values($cases);
    }
}
