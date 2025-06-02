<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Assembly;

use Boson\Component\CpuInfo\Architecture;
use Boson\Component\CpuInfo\ArchitectureInterface;
use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\FamilyInterface;

/**
 * @template-implements \IteratorAggregate<array-key, Assembly>
 */
final class AssemblyCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var list<Assembly>
     */
    private array $assemblies;

    /**
     * @phpstan-type OSFamilyType non-empty-string
     * @phpstan-type CPUArchitectureType non-empty-string
     * @phpstan-type PHPEditionType non-empty-string
     * @phpstan-type FrontendBinaryType non-empty-string
     * @phpstan-type BackendBinaryType non-empty-string
     *
     * @var array<OSFamilyType, array<CPUArchitectureType, array<PHPEditionType, array{
     *     FrontendBinaryType,
     *     BackendBinaryType
     * }>>>
     */
    private const array BUILTIN_ASSEMBLIES = [
        'linux' => [
            'arm64' => [
                'minimal' => ['libboson-linux-aarch64.so', 'minimal/linux-aarch64.sfx', ''],
                'standard' => ['libboson-linux-aarch64.so', 'standard/linux-aarch64.sfx', ''],
            ],
            'amd64' => [
                'minimal' => ['libboson-linux-x86_64.so', 'minimal/linux-x86_64.sfx', ''],
                'standard' => ['libboson-linux-x86_64.so', 'standard/linux-x86_64.sfx', ''],
            ],
        ],
        'darwin' => [
            'arm64' => [
                'minimal' => ['libboson-darwin-universal.dylib', 'minimal/macos-aarch64.sfx', ''],
                'standard' => ['libboson-darwin-universal.dylib', 'standard/macos-aarch64.sfx', ''],
            ],
            'amd64' => [
                'minimal' => ['libboson-darwin-universal.dylib', 'minimal/macos-x86_64.sfx', ''],
                'standard' => ['libboson-darwin-universal.dylib', 'standard/macos-x86_64.sfx', ''],
            ],
        ],
        'windows' => [
            'amd64' => [
                'minimal' => ['libboson-windows-x86_64.dll', 'minimal/windows-x86_64.sfx', 'exe'],
                'standard' => ['libboson-windows-x86_64.dll', 'standard/windows-x86_64.sfx', 'exe'],
            ],
        ],
    ];

    public function __construct(iterable $assemblies)
    {
        $this->assemblies = \iterator_to_array($assemblies, false);
    }

    public static function createFromBuiltinAssemblies(): self
    {
        $result = [];

        foreach (self::BUILTIN_ASSEMBLIES as $family => $cpus) {
            foreach ($cpus as $cpu => $editions) {
                foreach ($editions as $edition => [$frontend, $backend, $extension]) {
                    $result[] = new Assembly(
                        family: Family::from($family),
                        arch: Architecture::from($cpu),
                        edition: Edition::from($edition),
                        frontend: $frontend,
                        backend: $backend,
                        extension: $extension,
                    );
                }
            }
        }

        return new self($result);
    }

    /**
     * @param list<FamilyInterface> $families
     */
    public function withExpectedFamilies(array $families): self
    {
        $result = [];

        foreach ($this->assemblies as $assembly) {
            foreach ($families as $family) {
                if ($family === $assembly->family) {
                    $result[] = $assembly;
                }
            }
        }

        return new self($result);
    }

    /**
     * @return iterable<array-key, FamilyInterface>
     */
    public function getAvailableFamilies(): iterable
    {
        $family = [];

        foreach ($this->assemblies as $assembly) {
            $family[$assembly->family->name] = $assembly->family;
        }

        return \array_values($family);
    }

    /**
     * @param list<ArchitectureInterface> $architectures
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
     * @return iterable<array-key, ArchitectureInterface>
     */
    public function getAvailableArchitectures(): iterable
    {
        $architectures = [];

        foreach ($this->assemblies as $assembly) {
            $architectures[$assembly->arch->name] = $assembly->arch;
        }

        return \array_values($architectures);
    }

    public function withExpectedEdition(EditionInterface $edition): self
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
     * @return iterable<array-key, EditionInterface>
     */
    public function getAvailableEditions(): iterable
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
