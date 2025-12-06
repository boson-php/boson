<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Target;

use Boson\Component\Compiler\Target\Factory\BuiltinTargetFactory\BuiltinArchitectureTarget;

final readonly class LinuxBuiltinTarget extends UnixBuiltinTarget
{
    /**
     * @var non-empty-string
     */
    private const string DEFAULT_RUNTIME_AMD64_BINARY_NAME = 'libboson-linux-x86_64.so';

    /**
     * @var non-empty-string
     */
    private const string DEFAULT_RUNTIME_ARM64_BINARY_NAME = 'libboson-linux-aarch64.so';

    /**
     * @var non-empty-string
     */
    private const string MINIMAL_EDITION = 'min';

    /**
     * @var non-empty-string
     */
    private const string STANDARD_EDITION = 'standard';

    /**
     * @var list<non-empty-lowercase-string>
     */
    private const array MINIMAL_SFX_EXTENSIONS = [
        'ctype',
        'ffi',
        'filter',
        'iconv',
        'opcache',
        'phar',
        'shmop',
        'sockets',
        'zlib',
    ];

    /**
     * @var list<non-empty-lowercase-string>
     */
    private const array STANDARD_SFX_EXTENSIONS = [
        'ctype',
        'curl',
        'dom',
        'ffi',
        'filter',
        'iconv',
        'libxml',
        'mbstring',
        'opcache',
        'openssl',
        'pdo',
        'pdo_sqlite',
        'phar',
        'shmop',
        'sockets',
        'sodium',
        'sqlite3',
        'xml',
        'zlib',
    ];

    protected function getRuntimeBinaryFilename(): string
    {
        return match ($this->arch) {
            BuiltinArchitectureTarget::Amd64 => self::DEFAULT_RUNTIME_AMD64_BINARY_NAME,
            /** @phpstan-ignore-next-line : Allow default match arm */
            BuiltinArchitectureTarget::Arm64 => self::DEFAULT_RUNTIME_ARM64_BINARY_NAME,
            default => throw new \InvalidArgumentException(\sprintf(
                'Unsupported architecture "%s"',
                /** @phpstan-ignore-next-line : Backed enum's value is always string|int */
                $this->arch->value,
            )),
        };
    }

    protected function getSfxExtensionMapping(): array
    {
        return [
            self::MINIMAL_EDITION => self::MINIMAL_SFX_EXTENSIONS,
            self::STANDARD_EDITION => self::STANDARD_SFX_EXTENSIONS,
        ];
    }

    protected function getSfxFilename(string $edition): string
    {
        return match ($this->arch) {
            BuiltinArchitectureTarget::Amd64 => match ($edition) {
                self::MINIMAL_EDITION => 'linux-x86_64.min.sfx',
                self::STANDARD_EDITION => 'linux-x86_64.standard.sfx',
                default => throw new \RuntimeException(\sprintf('Unsupported edition "%s"', $edition)),
            },
            /** @phpstan-ignore-next-line : Allow default match arm */
            BuiltinArchitectureTarget::Arm64 => match ($edition) {
                self::MINIMAL_EDITION => 'linux-aarch64.min.sfx',
                self::STANDARD_EDITION => 'linux-aarch64.standard.sfx',
                default => throw new \RuntimeException(\sprintf('Unsupported edition "%s"', $edition)),
            },
            default => throw new \RuntimeException(\sprintf(
                'Unsupported architecture "%s"',
                /** @phpstan-ignore-next-line : Backed enum's value is always string|int */
                $this->arch->value,
            )),
        };
    }
}
