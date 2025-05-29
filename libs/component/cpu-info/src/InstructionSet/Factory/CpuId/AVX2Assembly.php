<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet\Factory\CpuId;

final readonly class AVX2Assembly extends AMD64Assembly
{
    public function __toString(): string
    {
        return "\xb8\x07\x00\x00\x00" // mov eax, 7
             . "\x31\xc9"             // xor ecx, ecx
             . "\x0f\xa2"             // cpuid
             . "\x0f\xba\xf3\x05"     // bt ebx, 5
             . "\x0f\x92\xc0"         // setc al
             . "\x0f\xb6\xc0"         // movzx eax, al
             . "\xc3";                // ret
    }
}
