<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo;

use Boson\Component\CpuInfo\InstructionSet\InstructionSetImpl;

final class InstructionSet implements InstructionSetInterface
{
    use InstructionSetImpl;

    public const InstructionSetInterface MMX = InstructionSet\MMX;
    public const InstructionSetInterface SSE = InstructionSet\SSE;
    public const InstructionSetInterface SSE2 = InstructionSet\SSE2;
    public const InstructionSetInterface SSE3 = InstructionSet\SSE3;
    public const InstructionSetInterface SSSE3 = InstructionSet\SSSE3;
    public const InstructionSetInterface SSE4_1 = InstructionSet\SSE4_1;
    public const InstructionSetInterface SSE4_2 = InstructionSet\SSE4_2;
    public const InstructionSetInterface FMA3 = InstructionSet\FMA3;
    public const InstructionSetInterface AVX = InstructionSet\AVX;
    public const InstructionSetInterface AVX2 = InstructionSet\AVX2;
    public const InstructionSetInterface AVX512 = InstructionSet\AVX512;
}
