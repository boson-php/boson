<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo;

use Boson\Component\CpuInfo\Architecture\ArchitectureImpl;
use Boson\Component\CpuInfo\Architecture\BuiltinArchitecture;
use Boson\Component\CpuInfo\Architecture\Factory\DefaultArchitectureFactory;
use Boson\Component\CpuInfo\Architecture\Factory\InMemoryArchitectureFactory;

require_once __DIR__ . '/Architecture/constants.php';

/**
 * Main class representing CPU architecture information.
 *
 * Provides a set of predefined CPU architectures and methods to work
 * with them. It supports both built-in architectures (like x86, ARM, etc.)
 * and custom architectures.
 */
final readonly class Architecture implements ArchitectureInterface
{
    use ArchitectureImpl;

    /**
     * Intel x86 architecture (32-bit)
     */
    public const ArchitectureInterface x86 = Architecture\X86;

    /**
     * AMD64 architecture (64-bit x86)
     */
    public const ArchitectureInterface Amd64 = Architecture\AMD64;

    /**
     * ARM architecture (32-bit)
     */
    public const ArchitectureInterface Arm = Architecture\ARM;

    /**
     * ARM64 architecture (64-bit ARM)
     */
    public const ArchitectureInterface Arm64 = Architecture\ARM64;

    /**
     * Intel Itanium architecture
     */
    public const ArchitectureInterface Itanium = Architecture\ITANIUM;

    /**
     * RISC-V architecture (32-bit)
     */
    public const ArchitectureInterface RiscV32 = Architecture\RISCV32;

    /**
     * RISC-V architecture (64-bit)
     */
    public const ArchitectureInterface RiscV64 = Architecture\RISCV64;

    /**
     * MIPS architecture (32-bit)
     */
    public const ArchitectureInterface Mips = Architecture\MIPS;

    /**
     * MIPS architecture (64-bit)
     */
    public const ArchitectureInterface Mips64 = Architecture\MIPS64;

    /**
     * PowerPC architecture (32-bit)
     */
    public const ArchitectureInterface PowerPc = Architecture\PPC;

    /**
     * PowerPC architecture (64-bit)
     */
    public const ArchitectureInterface PowerPc64 = Architecture\PPC64;

    /**
     * SPARC architecture (32-bit)
     */
    public const ArchitectureInterface Sparc = Architecture\SPARC;

    /**
     * SPARC architecture (64-bit)
     */
    public const ArchitectureInterface Sparc64 = Architecture\SPARC64;

    /**
     * Creates a new architecture instance based on the current system.
     *
     * Note: The result is cached in memory for subsequent calls.
     *
     * @api
     */
    public static function createFromGlobals(): ArchitectureInterface
    {
        /** @phpstan-var InMemoryArchitectureFactory $factory */
        static $factory = new InMemoryArchitectureFactory(
            delegate: new DefaultArchitectureFactory(),
        );

        return $factory->createArchitecture();
    }

    /**
     * Attempts to create a built-in architecture instance from a name.
     *
     * This method tries to match the given name against known architecture
     * names and their aliases. For example, 'x86_64' will match the AMD64
     * architecture.
     *
     * @api
     */
    public static function tryFrom(string $name): ?BuiltinArchitecture
    {
        return BuiltinArchitecture::tryFrom($name);
    }

    /**
     * Creates an architecture instance from a name.
     *
     * This method first attempts to find a matching built-in architecture.
     * If no match is found, it creates a new custom architecture with the
     * given name.
     *
     * @api
     */
    public static function from(string $name): ArchitectureInterface
    {
        return self::tryFrom($name) ?? new self($name);
    }

    /**
     * Returns a list of all built-in architectures.
     *
     * This method returns all the predefined architecture constants
     * defined in this class.
     *
     * @api
     *
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
