<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Architecture;

use Boson\Component\CpuInfo\Architecture;
use Boson\Component\CpuInfo\ArchitectureInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Component\CpuInfo\Architecture
 */
final class BuiltinArchitecture implements ArchitectureInterface
{
    use ArchitectureImpl;

    public static function tryFrom(string $name): ?BuiltinArchitecture
    {
        return [
            'x86' => Architecture::x86,
            'i386' => Architecture::x86,
            'ia32' => Architecture::x86,
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
        ][\strtolower($name)] ?? null;
    }
}
