<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Target;

use Boson\Component\Compiler\Configuration;
use Boson\Component\Compiler\Target\Factory\BuiltinTargetFactory\BuiltinArchitectureTarget;

final readonly class WindowsBuiltinTarget extends BuiltinTarget
{
    /**
     * @var non-empty-string
     */
    private const string DEFAULT_RUNTIME_BINARY_NAME = 'libboson-windows-x86_64.dll';

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
    protected const array MINIMAL_SFX_EXTENSIONS = [
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
    protected const array STANDARD_SFX_EXTENSIONS = [
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
        return self::DEFAULT_RUNTIME_BINARY_NAME;
    }

    protected function getTargetFilename(Configuration $config): string
    {
        return $config->name . '.exe';
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
                self::MINIMAL_EDITION => 'windows-x86_64.min.sfx',
                self::STANDARD_EDITION => 'windows-x86_64.standard.sfx',
                default => throw new \RuntimeException(\sprintf('Unsupported edition "%s"', $edition)),
            },
            default => throw new \RuntimeException(\sprintf(
                'Unsupported architecture "%s"',
                $this->arch->value,
            )),
        };
    }
}
