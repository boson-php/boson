<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet\Factory;

use Boson\Component\CpuInfo\ArchitectureInterface;
use Boson\Component\CpuInfo\InstructionSet;
use Boson\Component\CpuInfo\InstructionSet\Factory\CpuId\AVX2Assembly;
use Boson\Component\CpuInfo\InstructionSet\Factory\CpuId\AVX512FAssembly;
use Boson\Component\CpuInfo\InstructionSet\Factory\CpuId\AVXAssembly;
use Boson\Component\CpuInfo\InstructionSet\Factory\CpuId\CpuIdExecutorInterface;
use Boson\Component\CpuInfo\InstructionSet\Factory\CpuId\FMA3Assembly;
use Boson\Component\CpuInfo\InstructionSet\Factory\CpuId\MMXAssembly;
use Boson\Component\CpuInfo\InstructionSet\Factory\CpuId\SSE2Assembly;
use Boson\Component\CpuInfo\InstructionSet\Factory\CpuId\SSE3Assembly;
use Boson\Component\CpuInfo\InstructionSet\Factory\CpuId\SSE41Assembly;
use Boson\Component\CpuInfo\InstructionSet\Factory\CpuId\SSE42Assembly;
use Boson\Component\CpuInfo\InstructionSet\Factory\CpuId\SSEAssembly;
use Boson\Component\CpuInfo\InstructionSet\Factory\CpuId\SSSE3Assembly;
use Boson\Component\CpuInfo\InstructionSet\Factory\CpuId\Win32CpuIdExecutor;
use Boson\Component\CpuInfo\InstructionSetInterface;
use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\Family\Factory\FamilyFactoryInterface;

final readonly class CpuIdInstructionSetFactory implements InstructionSetFactoryInterface
{
    public function __construct(
        private InstructionSetFactoryInterface $delegate,
        private ?FamilyFactoryInterface $osFamilyFactory = null,
    ) {}

    private function getCpuIdExecutor(): ?CpuIdExecutorInterface
    {
        $family = $this->osFamilyFactory?->createFamily()
            ?? Family::createFromGlobals();

        return match (true) {
            $family->is(Family::Windows) => new Win32CpuIdExecutor(),
            default => null,
        };
    }

    public function createInstructionSets(ArchitectureInterface $arch): array
    {
        $fallback = $this->delegate->createInstructionSets($arch);

        $executor = $this->getCpuIdExecutor();

        if ($executor === null) {
            return $fallback;
        }

        return \array_values(\array_unique([
            ...$fallback,
            ...$this->tryCreateFromCpuId($executor),
        ]));
    }

    /**
     * @return list<InstructionSetInterface>
     */
    private function tryCreateFromCpuId(CpuIdExecutorInterface $executor): array
    {
        $result = [];

        if ($executor->execute(new MMXAssembly())) {
            $result[] = InstructionSet::MMX;
        }

        if ($executor->execute(new SSEAssembly())) {
            $result[] = InstructionSet::SSE;
        }

        if ($executor->execute(new SSE2Assembly())) {
            $result[] = InstructionSet::SSE2;
        }

        if ($executor->execute(new SSE3Assembly())) {
            $result[] = InstructionSet::SSE3;
        }

        if ($executor->execute(new SSSE3Assembly())) {
            $result[] = InstructionSet::SSSE3;
        }

        if ($executor->execute(new SSE41Assembly())) {
            $result[] = InstructionSet::SSE4_1;
        }

        if ($executor->execute(new SSE42Assembly())) {
            $result[] = InstructionSet::SSE4_2;
        }

        if ($executor->execute(new FMA3Assembly())) {
            $result[] = InstructionSet::FMA3;
        }

        if ($executor->execute(new AVXAssembly())) {
            $result[] = InstructionSet::AVX;
        }

        if ($executor->execute(new AVX2Assembly())) {
            $result[] = InstructionSet::AVX2;
        }

        if ($executor->execute(new AVX512FAssembly())) {
            $result[] = InstructionSet::AVX512;
        }

        return $result;
    }
}
