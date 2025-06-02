<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet;

use Boson\Component\CpuInfo\InstructionSet;
use Boson\Component\CpuInfo\InstructionSetInterface;

final readonly class BuiltinInstructionSet implements InstructionSetInterface
{
    use InstructionSetImpl;

    public static function tryFrom(string $name): ?BuiltinInstructionSet
    {
        return [
            'mmx' => InstructionSet::MMX,
            'sse' => InstructionSet::SSE,
            'sse2' => InstructionSet::SSE2,
            'pni' => InstructionSet::SSE3,
            'sse3' => InstructionSet::SSE3,
            'ssse3' => InstructionSet::SSSE3,
            'sse4_1' => InstructionSet::SSE4_1,
            'sse4_2' => InstructionSet::SSE4_2,
            'avx' => InstructionSet::AVX,
            'avx2' => InstructionSet::AVX2,
            'avx512f' => InstructionSet::AVX512,
        ][\strtolower($name)] ?? null;
    }
}
