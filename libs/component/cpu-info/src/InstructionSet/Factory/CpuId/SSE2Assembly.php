<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet\Factory\CpuId;

final readonly class SSE2Assembly extends AMD64Assembly
{
    public function __toString(): string
    {
        return "\xB8\x01\x00\x00\x00"      // mov    eax,0x1
             . "\x0F\xA2"                  // cpuid
             . "\xF7\xC2\x00\x00\x40\x00"  // test   edx,0x04000000 (1 << 26)
             . "\x74\x05"                  // jz     no_sse2
             . "\xB0\x01"                  // mov    al,0x1
             . "\xC3"                      // ret
             . "\x30\xC0"                  // xor    al,al
             . "\xC3";                     // ret
    }
}
