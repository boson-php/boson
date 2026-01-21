<?php

declare(strict_types=1);

namespace Boson\Api\CentralProcessor;

use Boson\Component\CpuInfo\ArchitectureInterface;
use Boson\Component\CpuInfo\CentralProcessor;
use Boson\Component\CpuInfo\InstructionSetInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Api\CentralProcessor
 */
final class CentralProcessorInfo implements CentralProcessorInfoInterface
{
    private CentralProcessor $cpu {
        get => $this->cpu ??= CentralProcessor::createFromGlobals();
    }

    public ArchitectureInterface $arch {
        get => $this->cpu->arch;
    }

    public string $vendor {
        get => $this->cpu->vendor;
    }

    public ?string $name {
        get => $this->cpu->name;
    }

    public int $cores {
        get => $this->cpu->cores;
    }

    public int $threads {
        get => $this->cpu->threads;
    }

    public iterable $instructionSets {
        get => $this->cpu->instructionSets;
    }

    public function isSupports(InstructionSetInterface $instructionSet): bool
    {
        return $this->cpu->isSupports($instructionSet);
    }

    public function __debugInfo(): array
    {
        return [
            'arch' => $this->arch,
            'vendor' => $this->vendor,
            'name' => $this->name,
            'cores' => $this->cores,
            'threads' => $this->threads,
            'instructionSets' => $this->instructionSets,
        ];
    }
}
