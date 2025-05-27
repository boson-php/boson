<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Architecture\Factory;

use Boson\Component\CpuInfo\Architecture;
use Boson\Component\CpuInfo\ArchitectureInterface;

abstract readonly class ArchitectureByNameFactory implements ArchitectureFactoryInterface
{
    /**
     * @link https://wiki.debian.org/ArchitectureSpecificsMemo
     * @link https://doc.rust-lang.org/std/arch/index.html
     *
     * @var array<non-empty-lowercase-string, ArchitectureInterface>
     * @phpstan-ignore-next-line PHPStan false-positive
     */
    private const array UNAME_MAPPINGS = [
        'x86' => Architecture::x86,
        'i386' => Architecture::x86,
        'ia32' => Architecture::x86,
        'AMD64' => Architecture::Amd64,
        'amd64' => Architecture::Amd64,
        'x64' => Architecture::Amd64,
        'x86_64' => Architecture::Amd64,
        'arm64' => Architecture::Arm64,
        'aarch64' => Architecture::Arm64,
        'arm64ilp32' => Architecture::Arm64,
        'arm' => Architecture::Arm,
        'armel' => Architecture::Arm,
        'armhf' => Architecture::Arm,
        'mips' => Architecture::Mips,
        'mipsel' => Architecture::Mips,
        'mips64' => Architecture::Mips64,
        'mips64el' => Architecture::Mips64,
        'ppc' => Architecture::PowerPc,
        'powerpc' => Architecture::PowerPc,
        'powerpcspe' => Architecture::PowerPc,
        'ppc64' => Architecture::PowerPc64,
        'ppc64el' => Architecture::PowerPc64,
        'riscv64' => Architecture::RiscV64,
        'sparc' => Architecture::Sparc,
        'sparc64' => Architecture::Sparc64,
        'ia64' => Architecture::Itanium,
    ];

    /**
     * @param non-empty-string $name
     */
    protected function createFromName(string $name): ArchitectureInterface
    {
        return self::UNAME_MAPPINGS[\strtolower($name)]
            ?? new Architecture($name);
    }
}
