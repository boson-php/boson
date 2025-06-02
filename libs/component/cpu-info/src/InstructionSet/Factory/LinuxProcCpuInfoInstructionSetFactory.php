<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet\Factory;

use Boson\Component\CpuInfo\ArchitectureInterface;
use Boson\Component\CpuInfo\InstructionSet;
use Boson\Component\CpuInfo\InstructionSetInterface;
use Boson\Component\CpuInfo\Internal\LinuxProcCpuInfo;
use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\Family\Factory\FamilyFactoryInterface;

final readonly class LinuxProcCpuInfoInstructionSetFactory implements InstructionSetFactoryInterface
{
    public function __construct(
        private InstructionSetFactoryInterface $delegate,
        private ?FamilyFactoryInterface $osFamilyFactory = null,
    ) {}

    public function createInstructionSets(ArchitectureInterface $arch): array
    {
        $family = $this->osFamilyFactory?->createFamily()
            ?? Family::createFromGlobals();

        $fallback = $this->delegate->createInstructionSets($arch);

        if (!$family->is(Family::Linux) || !LinuxProcCpuInfo::isReadable()) {
            return $fallback;
        }

        return \array_values(\array_unique([
            ...$fallback,
            ...$this->tryCreateFromProcCpuInfo($arch),
        ]));
    }

    /**
     * @return list<InstructionSetInterface>
     */
    private function tryCreateFromProcCpuInfo(ArchitectureInterface $arch): array
    {
        $processors = new LinuxProcCpuInfo()
            ->getSegmentsByPhysicalId();

        $result = [];

        foreach ($processors as $processor) {
            foreach ($processor as $core) {
                $flags = $core['flags'] ?? '';

                foreach ($this->parseFlags($flags, $arch) as $flag => $set) {
                    $result[$flag] = $set;
                }
            }
        }

        return \array_values($result);
    }

    /**
     * @return iterable<non-empty-string, InstructionSetInterface>
     */
    private function parseFlags(string $flags, ArchitectureInterface $arch): iterable
    {
        /**
         * @link https://git.kernel.org/pub/scm/linux/kernel/git/stable/linux.git/tree/arch/x86/include/asm/cpufeature.h?id=refs/tags/v4.1.3
         */
        foreach (\explode(' ', $flags) as $flag) {
            if ($flag === '') {
                continue;
            }

            yield $flag => match (\strtolower($flag)) {
                'mmx' => InstructionSet::MMX,
                'sse' => InstructionSet::SSE,
                'sse2' => InstructionSet::SSE2,
                'pni', 'sse3' => InstructionSet::SSE3,
                'ssse3' => InstructionSet::SSSE3,
                'sse4_1' => InstructionSet::SSE4_1,
                'sse4_2' => InstructionSet::SSE4_2,
                'avx' => InstructionSet::AVX,
                'avx2' => InstructionSet::AVX2,
                'avx512f' => InstructionSet::AVX512,
                default => new InstructionSet($flag),
            };
        }
    }
}
