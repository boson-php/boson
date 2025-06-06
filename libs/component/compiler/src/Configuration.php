<?php

declare(strict_types=1);

namespace Boson\Component\Compiler;

use Boson\Component\Compiler\Assembly\AssemblyArchitecture;
use Boson\Component\Compiler\Assembly\AssemblyPlatform;
use Boson\Component\Compiler\Configuration\IncludeConfiguration;

final class Configuration
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_APP_NAME = 'app';

    /**
     * @var non-empty-string
     */
    public const string DEFAULT_ENTRYPOINT = 'index.php';
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_BOX_VERSION = '4.6.6';

    /**
     * @var non-empty-string|null
     */
    public const ?string DEFAULT_BUILD_DIRECTORY = null;

    /**
     * @var non-empty-string|null
     */
    public const ?string DEFAULT_APP_DIRECTORY = null;

    /**
     * @var list<AssemblyArchitecture>
     */
    public private(set) array $architectures;

    /**
     * @var list<AssemblyPlatform>
     */
    public private(set) array $platforms;

    /**
     * @var list<IncludeConfiguration>
     */
    public private(set) array $build;

    /**
     * @var array<non-empty-string, scalar>
     */
    public private(set) array $ini;

    /**
     * @var non-empty-string
     */
    public private(set) string $output {
        get => $this->output;
        set(?string $directory) => $directory
            ?? ($this->root . \DIRECTORY_SEPARATOR . 'build');
    }

    /**
     * @var non-empty-string
     */
    public private(set) string $root {
        /** @phpstan-ignore-next-line : Root cannot be empty */
        get => $this->root ??= (\getcwd() ?: '.');
        /** @phpstan-ignore-next-line : Root cannot be empty */
        set(?string $directory) => $directory ?? (\getcwd() ?: '.');
    }

    /**
     * @var non-empty-string
     */
    public string $pharName {
        get => $this->name . '.phar';
    }

    /**
     * @var non-empty-string
     */
    public string $pharPathname {
        get => $this->output . \DIRECTORY_SEPARATOR . $this->pharName;
    }

    /**
     * @var non-empty-string
     */
    public string $boxStubName {
        get => 'entrypoint.php';
    }

    /**
     * @var non-empty-string
     */
    public string $boxStubPathname {
        get => $this->output . \DIRECTORY_SEPARATOR . $this->boxStubName;
    }

    /**
     * @var non-empty-string
     */
    public string $boxConfigName {
        get => 'box.json';
    }

    /**
     * @var non-empty-string
     */
    public string $boxConfigPathname {
        get => $this->output . \DIRECTORY_SEPARATOR . $this->boxConfigName;
    }

    /**
     * @var non-empty-string
     */
    public string $boxPharName {
        get => 'box-' . $this->boxVersion . '.phar';
    }

    /**
     * @var non-empty-string
     */
    public string $boxPharPathname {
        get => $this->output . \DIRECTORY_SEPARATOR . $this->boxPharName;
    }

    /**
     * @var non-empty-string
     */
    public string $boxUri {
        get => \vsprintf('https://github.com/box-project/box/releases/download/%s/box.phar', [
            $this->boxVersion,
        ]);
    }

    /**
     * @param iterable<mixed, IncludeConfiguration> $build
     * @param iterable<non-empty-string, scalar> $ini
     * @param iterable<mixed, AssemblyArchitecture> $architectures
     * @param iterable<mixed, AssemblyPlatform> $platforms
     * @param non-empty-string|null $output
     * @param non-empty-string|null $root
     */
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

    public static function createDefaultConfiguration(): self
    {
        return new self();
    }

    /**
     * @param non-empty-string $name
     */
    public function withName(string $name): self
    {
        $self = clone $this;
        $self->name = $name;

        return $self;
    }

    /**
     * @param non-empty-string $entrypoint
     */
    public function withEntrypoint(string $entrypoint): self
    {
        $self = clone $this;
        $self->entrypoint = $entrypoint;

        return $self;
    }

    /**
     * @param non-empty-string $version
     */
    public function withBoxVersion(string $version): self
    {
        $self = clone $this;
        $self->boxVersion = $version;

        return $self;
    }

    /**
     * @param non-empty-string|null $directory
     */
    public function withOutputDirectory(?string $directory): self
    {
        $self = clone $this;
        $self->output = $directory;

        return $self;
    }

    /**
     * @param non-empty-string|null $directory
     */
    public function withRootDirectory(?string $directory): self
    {
        $self = clone $this;
        $self->root = $directory;

        return $self;
    }

    /**
     * @param non-empty-string $config
     */
    public function withAddedIni(string $config, string|float|bool|int $value): self
    {
        $self = clone $this;
        $self->ini[$config] = $value;

        return $self;
    }

    public function withAddedBuildInclusion(IncludeConfiguration $config): self
    {
        $self = clone $this;
        $self->build[] = $config;

        return $self;
    }

    /**
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
