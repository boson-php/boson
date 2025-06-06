<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Assembly;

/**
 * @template-implements \IteratorAggregate<array-key, Assembly>
 */
final class AssemblyCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var list<Assembly>
     */
    private array $assemblies;

    private const array BUILTIN_ASSEMBLIES = [
        'linux' => [
            'arm64' => [
                'minimal' => ['libboson-linux-aarch64.so', 'minimal/linux-aarch64.sfx'],
                'standard' => ['libboson-linux-aarch64.so', 'standard/linux-aarch64.sfx'],
            ],
            'amd64' => [
                'minimal' => ['libboson-linux-x86_64.so', 'minimal/linux-x86_64.sfx'],
                'standard' => ['libboson-linux-x86_64.so', 'standard/linux-x86_64.sfx'],
            ],
        ],
        'macos' => [
            'arm64' => [
                'minimal' => ['libboson-darwin-universal.dylib', 'minimal/macos-aarch64.sfx'],
                'standard' => ['libboson-darwin-universal.dylib', 'standard/macos-aarch64.sfx'],
            ],
            'amd64' => [
                'minimal' => ['libboson-darwin-universal.dylib', 'minimal/macos-x86_64.sfx'],
                'standard' => ['libboson-darwin-universal.dylib', 'standard/macos-x86_64.sfx'],
            ],
        ],
        'windows' => [
            'amd64' => [
                'minimal' => ['libboson-windows-x86_64.dll', 'minimal/windows-x86_64.sfx'],
                'standard' => ['libboson-windows-x86_64.dll', 'standard/windows-x86_64.sfx'],
            ],
        ],
    ];

    /**
     * @param iterable<mixed, Assembly> $assemblies
     */
    public function __construct(iterable $assemblies)
    {
        $this->assemblies = \iterator_to_array($assemblies, false);
    }

    public static function createFromBuiltinAssemblies(): self
    {
        $result = [];

        foreach (self::BUILTIN_ASSEMBLIES as $family => $cpus) {
            foreach ($cpus as $cpu => $editions) {
                foreach ($editions as $edition => [$frontend, $backend]) {
                    $result[] = new Assembly(
                        platform: AssemblyPlatform::fromNormalized($family),
                        arch: AssemblyArchitecture::fromNormalized($cpu),
                        edition: AssemblyEdition::fromNormalized($edition),
                        frontend: $frontend,
                        backend: $backend,
                    );
                }
            }
        }

        return new self($result);
    }

    /**
     * @param list<AssemblyPlatform> $platforms
     */
    public function withExpectedPlatforms(array $platforms): self
    {
        $result = [];

        foreach ($this->assemblies as $assembly) {
            foreach ($platforms as $platform) {
                if ($platform === $assembly->platform) {
                    $result[] = $assembly;
                }
            }
        }

        return new self($result);
    }

    /**
     * @return list<AssemblyPlatform>
     */
    public function getAvailablePlatforms(): array
    {
        $family = [];

        foreach ($this->assemblies as $assembly) {
            $family[$assembly->platform->name] = $assembly->platform;
        }

        return \array_values($family);
    }

    /**
     * @param list<AssemblyArchitecture> $architectures
     */
    public function withExpectedArchitectures(array $architectures): self
    {
        $result = [];

        foreach ($this->assemblies as $assembly) {
            foreach ($architectures as $architecture) {
                if ($architecture === $assembly->arch) {
                    $result[] = $assembly;
                }
            }
        }

        return new self($result);
    }

    /**
     * @return list<AssemblyArchitecture>
     */
    public function getAvailableArchitectures(): array
    {
        $architectures = [];

        foreach ($this->assemblies as $assembly) {
            $architectures[$assembly->arch->name] = $assembly->arch;
        }

        return \array_values($architectures);
    }

    public function withExpectedEdition(AssemblyEdition $edition): self
    {
        $result = [];

        foreach ($this->assemblies as $assembly) {
            if ($edition === $assembly->edition) {
                $result[] = $assembly;
            }
        }

        return new self($result);
    }

    /**
     * @return list<AssemblyEdition>
     */
    public function getAvailableEditions(): array
    {
        $editions = [];

        foreach ($this->assemblies as $assembly) {
            $editions[$assembly->edition->name] = $assembly->edition;
        }

        return \array_values($editions);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->assemblies);
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return \count($this->assemblies);
    }
}
