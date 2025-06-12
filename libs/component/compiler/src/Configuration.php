<?php

declare(strict_types=1);

namespace Boson\Component\Compiler;

use Boson\Component\Compiler\Assembly\AssemblyArchitecture;
use Boson\Component\Compiler\Assembly\AssemblyPlatform;
use Boson\Component\Compiler\Configuration\IncludeConfiguration;

/**
 * Configuration class for managing compiler settings and build parameters.
 */
final class Configuration
{
    /**
     * Default application name used when not specified.
     *
     * @var non-empty-string
     */
    public const string DEFAULT_APP_NAME = 'app';

    /**
     * Default entrypoint file used when not specified.
     *
     * @var non-empty-string
     */
    public const string DEFAULT_ENTRYPOINT = 'index.php';

    /**
     * Default `humbug/box` version used when not specified.
     *
     * @var non-empty-string
     */
    public const string DEFAULT_BOX_VERSION = '4.6.6';

    /**
     * Default build directory used when not specified.
     *
     * @var non-empty-string|null
     */
    public const ?string DEFAULT_BUILD_DIRECTORY = null;

    /**
     * Default application directory used when not specified.
     *
     * @var non-empty-string|null
     */
    public const ?string DEFAULT_APP_DIRECTORY = null;

    /**
     * List of target CPU architectures for compilation.
     *
     * @var list<AssemblyArchitecture>
     */
    public private(set) array $architectures;

    /**
     * List of target operating system platforms for compilation.
     *
     * @var list<AssemblyPlatform>
     */
    public private(set) array $platforms;

    /**
     * List of build inclusion configurations.
     *
     * @var list<IncludeConfiguration>
     */
    public private(set) array $build;

    /**
     * PHP INI settings to be applied during compilation.
     *
     * @var array<non-empty-string, scalar>
     */
    public private(set) array $ini;

    /**
     * Output directory for compiled files.
     *
     * If not specified, defaults to 'build' subdirectory in root.
     *
     * @var non-empty-string
     */
    public private(set) string $output {
        get => $this->output;
        set(?string $directory) => $directory
            ?? ($this->root . \DIRECTORY_SEPARATOR . 'build');
    }

    /**
     * Root directory of the application.
     *
     * If not specified, defaults to current working directory.
     *
     * @var non-empty-string
     */
    public private(set) string $root {
        /** @phpstan-ignore-next-line : Root cannot be empty */
        get => $this->root ??= (\getcwd() ?: '.');
        /** @phpstan-ignore-next-line : Root cannot be empty */
        set(?string $directory) => $directory ?? (\getcwd() ?: '.');
    }

    /**
     * Name of the generated PHAR archive.
     *
     * @var non-empty-string
     */
    public string $pharName {
        get => $this->name . '.phar';
    }

    /**
     * Full path to the generated PHAR archive.
     *
     * @var non-empty-string
     */
    public string $pharPathname {
        get => $this->output . \DIRECTORY_SEPARATOR . $this->pharName;
    }

    /**
     * Name of the `humbug/box` stub file.
     *
     * @var non-empty-string
     */
    public string $boxStubName {
        get => 'entrypoint.php';
    }

    /**
     * Full path to the `humbug/box` stub file.
     *
     * @var non-empty-string
     */
    public string $boxStubPathname {
        get => $this->output . \DIRECTORY_SEPARATOR . $this->boxStubName;
    }

    /**
     * Name of the `humbug/box` configuration file.
     *
     * @var non-empty-string
     */
    public string $boxConfigName {
        get => 'box.json';
    }

    /**
     * Full path to the `humbug/box` configuration file.
     *
     * @var non-empty-string
     */
    public string $boxConfigPathname {
        get => $this->output . \DIRECTORY_SEPARATOR . $this->boxConfigName;
    }

    /**
     * Name of the `humbug/box` PHAR archive.
     *
     * @var non-empty-string
     */
    public string $boxPharName {
        get => 'box-' . $this->boxVersion . '.phar';
    }

    /**
     * Full path to the `humbug/box` PHAR archive.
     *
     * @var non-empty-string
     */
    public string $boxPharPathname {
        get => $this->output . \DIRECTORY_SEPARATOR . $this->boxPharName;
    }

    /**
     * URI for downloading the `humbug/box` PHAR archive.
     *
     * @var non-empty-string
     */
    public string $boxUri {
        get => \vsprintf('https://github.com/box-project/box/releases/download/%s/box.phar', [
            $this->boxVersion,
        ]);
    }

    public function __construct(
        /**
         * @var non-empty-string
         */
        public private(set) string $name = self::DEFAULT_APP_NAME,
        /**
         * @var non-empty-string
         */
        public private(set) string $entrypoint = self::DEFAULT_ENTRYPOINT,
        /**
         * @var non-empty-string
         */
        public private(set) string $boxVersion = self::DEFAULT_BOX_VERSION,
        ?string $output = self::DEFAULT_BUILD_DIRECTORY,
        ?string $root = self::DEFAULT_APP_DIRECTORY,
        iterable $architectures = [],
        iterable $platforms = [],
        iterable $build = [],
        iterable $ini = [],
    ) {
        $this->build = \iterator_to_array($build, false);
        $this->ini = \iterator_to_array($ini, true);
        $this->architectures = \iterator_to_array($architectures, false);
        $this->platforms = \iterator_to_array($platforms, false);
        $this->output = $output;
        $this->root = $root;
    }

    /**
     * Creates a new instance of {@see Configuration} with default values.
     */
    public static function createDefaultConfiguration(): self
    {
        return new self();
    }

    /**
     * Returns copy of an instance with updated application name.
     *
     * @param non-empty-string $name
     */
    public function withName(string $name): self
    {
        $self = clone $this;
        $self->name = $name;

        return $self;
    }

    /**
     * Returns copy of an instance with updated entrypoint file.
     *
     * @param non-empty-string $entrypoint
     */
    public function withEntrypoint(string $entrypoint): self
    {
        $self = clone $this;
        $self->entrypoint = $entrypoint;

        return $self;
    }

    /**
     * Returns copy of an instance with updated Box version.
     *
     * @param non-empty-string $version
     */
    public function withBoxVersion(string $version): self
    {
        $self = clone $this;
        $self->boxVersion = $version;

        return $self;
    }

    /**
     * Returns copy of an instance with updated output directory.
     *
     * @param non-empty-string|null $directory
     */
    public function withOutputDirectory(?string $directory): self
    {
        $self = clone $this;
        $self->output = $directory;

        return $self;
    }

    /**
     * Returns copy of an instance with updated root directory.
     *
     * @param non-empty-string|null $directory
     */
    public function withRootDirectory(?string $directory): self
    {
        $self = clone $this;
        $self->root = $directory;

        return $self;
    }

    /**
     * Returns copy of an instance with an additional PHP INI setting.
     *
     * @param non-empty-string $config
     */
    public function withAddedIni(string $config, string|float|bool|int $value): self
    {
        $self = clone $this;
        $self->ini[$config] = $value;

        return $self;
    }

    /**
     * Returns copy of an instance with an additional build inclusion.
     */
    public function withAddedBuildInclusion(IncludeConfiguration $config): self
    {
        $self = clone $this;
        $self->build[] = $config;

        return $self;
    }

    /**
     * Returns copy of an instance with updated architecture targets.
     *
     * @param iterable<mixed, AssemblyArchitecture|non-empty-string> $architectures
     */
    public function withArchitectures(iterable $architectures): self
    {
        $self = clone $this;
        $self->architectures = [];

        foreach ($architectures as $architecture) {
            if (\is_string($architecture)) {
                $architecture = AssemblyArchitecture::tryFromNormalized($architecture);
            }

            if ($architecture !== null) {
                $self->architectures[] = $architecture;
            }
        }

        return $self;
    }

    /**
     * Returns copy of an instance with updated platform targets.
     *
     * @param iterable<mixed, AssemblyPlatform|non-empty-string> $platforms
     */
    public function withPlatforms(iterable $platforms): self
    {
        $self = clone $this;
        $self->platforms = [];

        foreach ($platforms as $platform) {
            if (\is_string($platform)) {
                $platform = AssemblyPlatform::tryFromNormalized($platform);
            }

            if ($platform !== null) {
                $self->platforms[] = $platform;
            }
        }

        return $self;
    }
}
