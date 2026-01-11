<?php

declare(strict_types=1);

namespace Boson\Component\Saucer\Loader;

use Boson\Component\Saucer\Exception\Environment\UnsupportedArchitectureException;
use Boson\Component\Saucer\Exception\Environment\UnsupportedOperatingSystemException;

final class LibraryDetector implements \Stringable
{
    public const string DEFAULT_BIN_DIR = __DIR__ . '/../../bin';

    public const ?string DEFAULT_PHAR_DIR = null;

    /**
     * @var non-empty-string
     */
    public string $name {
        get {
            $os = $this->os ?? OperatingSystem::createFromGlobals();
            $arch = $this->arch ?? CpuArchitecture::createFromGlobals();
            $de = $this->de ?? DesktopEnvironment::createFromGlobals($os);

            return match ($os) {
                OperatingSystem::Windows => match ($arch) {
                    CpuArchitecture::x86,
                    CpuArchitecture::Amd64 => 'libboson-windows-x86_64.dll',
                    default => throw UnsupportedArchitectureException::becauseArchitectureIsInvalid(
                        architecture: \php_uname('m'),
                    ),
                },
                OperatingSystem::Linux,
                OperatingSystem::BSD => match ($arch) {
                    CpuArchitecture::x86,
                    CpuArchitecture::Amd64 => match ($de) {
                        DesktopEnvironment::QT => 'libboson-linux-qt-x86_64.so',
                        default => 'libboson-linux-gtk-x86_64.so',
                    },
                    default => throw UnsupportedArchitectureException::becauseArchitectureIsInvalid(
                        architecture: \php_uname('m'),
                    ),
                },
                OperatingSystem::MacOS => match ($arch) {
                    CpuArchitecture::x86,
                    CpuArchitecture::Amd64,
                    CpuArchitecture::Arm,
                    CpuArchitecture::Arm64 => 'libboson-darwin-universal.dylib',
                    default => throw UnsupportedArchitectureException::becauseArchitectureIsInvalid(
                        architecture: \php_uname('m'),
                    ),
                },
                default => null,
            } ?? throw UnsupportedOperatingSystemException::becauseOperatingSystemIsInvalid(
                os: \PHP_OS_FAMILY,
            );
        }
    }

    /**
     * @var non-empty-string
     */
    public string $directory {
        get {
            if (!\extension_loaded('phar') || \Phar::running() === '') {
                return $this->localDirectory;
            }

            $directory = \dirname(\Phar::running(false));

            if ($this->pharDirectory !== null) {
                return $directory . '/' . $this->pharDirectory;
            }

            /** @var non-empty-string */
            return $directory;
        }
    }

    /**
     * @phpstan-pure
     */
    public function __construct(
        private readonly ?OperatingSystem $os = null,
        private readonly ?CpuArchitecture $arch = null,
        private readonly ?DesktopEnvironment $de = null,
        /**
         * @var non-empty-string
         */
        private readonly string $localDirectory = self::DEFAULT_BIN_DIR,
        /**
         * @var non-empty-string|null
         */
        private readonly ?string $pharDirectory = self::DEFAULT_PHAR_DIR,
    ) {}

    /**
     * @api
     * @phpstan-pure
     */
    public function withOperatingSystem(?OperatingSystem $os): self
    {
        return new self(
            os: $os,
            arch: $this->arch,
            de: $this->de,
            localDirectory: $this->localDirectory,
            pharDirectory: $this->pharDirectory,
        );
    }

    /**
     * @api
     * @phpstan-pure
     */
    public function withCpuArchitecture(?CpuArchitecture $arch): self
    {
        return new self(
            os: $this->os,
            arch: $arch,
            de: $this->de,
            localDirectory: $this->localDirectory,
            pharDirectory: $this->pharDirectory,
        );
    }

    /**
     * @api
     * @phpstan-pure
     */
    public function withDesktopEnvironment(?DesktopEnvironment $de): self
    {
        return new self(
            os: $this->os,
            arch: $this->arch,
            de: $de,
            localDirectory: $this->localDirectory,
            pharDirectory: $this->pharDirectory,
        );
    }

    /**
     * @api
     * @phpstan-pure
     *
     * @param non-empty-string $directory
     */
    public function withLocalDirectory(string $directory): self
    {
        return new self(
            os: $this->os,
            arch: $this->arch,
            de: $this->de,
            localDirectory: $directory,
            pharDirectory: $this->pharDirectory,
        );
    }

    /**
     * @api
     * @phpstan-pure
     *
     * @param non-empty-string $directory
     */
    public function withPharDirectory(string $directory): self
    {
        return new self(
            os: $this->os,
            arch: $this->arch,
            de: $this->de,
            localDirectory: $this->localDirectory,
            pharDirectory: $directory,
        );
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->directory . '/' . $this->name;
    }
}
