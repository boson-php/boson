<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet\Factory\CpuId;

final readonly class SSSE3Assembly extends AMD64Assembly
{
    public function __toString(): string
    {
        return "\xB8\x01\x00\x00\x00"      // mov    eax,0x1
             . "\x0F\xA2"                  // cpuid
             . "\xF7\xC1\x00\x02\x00\x00"  // test   ecx,0x00000200 (1 << 9)
             . "\x74\x05"                  // jz     no_ssse3
             . "\xB0\x01"                  // mov    al,0x1
             . "\xC3"                      // ret
             . "\x30\xC0"                  // xor    al,al
             . "\xC3";                     // ret
    }
}
