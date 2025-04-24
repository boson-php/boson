<?php

declare(strict_types=1);

namespace Boson\Environment;

enum Architecture: string
{
    case x86 = 'x86';
    case Amd64 = 'amd64';
    case Arm = 'arm';
    case Arm64 = 'aarch64';
    case Itanium = 'ia64';
    case RiscV32 = 'riscv32';
    case RiscV64 = 'riscv64';
    case Mips = 'mips';
    case Mips64 = 'mips64';
    case PowerPc = 'ppc';
    case PowerPc64 = 'ppc64';
    case Sparc = 'sparc';
    case Sparc64 = 'sparc64';

    /**
     * @link https://wiki.debian.org/ArchitectureSpecificsMemo
     * @link https://doc.rust-lang.org/std/arch/index.html
     *
     * @var array<non-empty-string, self>
     */
    private const array UNAME_MAPPINGS = [
        'x86' => self::x86,
        'i386' => self::x86,
        'ia32' => self::x86,
        'AMD64' => self::Amd64,
        'amd64' => self::Amd64,
        'x64' => self::Amd64,
        'x86_64' => self::Amd64,
        'arm64' => self::Arm64,
        'aarch64' => self::Arm64,
        'arm64ilp32' => self::Arm64,
        'arm' => self::Arm,
        'armel' => self::Arm,
        'armhf' => self::Arm,
        'mips' => self::Mips,
        'mipsel' => self::Mips,
        'mips64' => self::Mips64,
        'mips64el' => self::Mips64,
        'ppc' => self::PowerPc,
        'powerpc' => self::PowerPc,
        'powerpcspe' => self::PowerPc,
        'ppc64' => self::PowerPc64,
        'ppc64el' => self::PowerPc64,
        'riscv64' => self::RiscV64,
        'sparc' => self::Sparc,
        'sparc64' => self::Sparc64,
        'ia64' => self::Itanium,
    ];

    /**
     * @var list<non-empty-string>
     */
    private const array UNAME_ENV_DEFS = [
        'BOSON_CPU_ARCH',
        'PROCESSOR_ARCHITECTURE',
    ];

    public static function current(): ?self
    {
        static $current = self::getArchitectureCaseFromEnvironment();

        /** @var self|null */
        return $current;
    }

    private static function getArchitectureCaseFromEnvironment(): ?self
    {
        $uname = self::getArchitectureStringFromEnvironment();

        return self::UNAME_MAPPINGS[$uname] ?? null;
    }

    /**
     * @return non-empty-string
     */
    private static function getArchitectureStringFromEnvironment(): string
    {
        foreach (self::UNAME_ENV_DEFS as $env) {
            if (isset($_SERVER[$env]) && \is_string($_SERVER[$env]) && $_SERVER[$env] !== '') {
                return $_SERVER[$env];
            }
        }

        /** @var non-empty-string */
        return \php_uname('m');
    }
}
