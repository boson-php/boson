<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet\Factory\CpuId;

final readonly class AVXAssembly extends AMD64Assembly
{
    public function __toString(): string
    {
        return "\xB8\x01\x00\x00\x00"      // mov eax,0x1
             . "\x0F\xA2"                  // cpuid
             . "\xF6\xC1\x80"              // test cl,0x80 ; bit 7 of ECX
             . "\x74\x17"                  // jz no_avx
             . "\xF6\xC1\x10"              // test cl,0x10 ; bit 4 of ECX (placeholder)
             . "\x74\x11"                  // jz no_avx
             . "\xB9\x00\x00\x00\x00"      // mov ecx,0
             . "\x0F\x01\xD0"              // xgetbv
             . "\x83\xE0\x06"              // and eax,0x6
             . "\x83\xF8\x06"              // cmp eax,0x6
             . "\x75\x06"                  // jne no_avx
             . "\xB0\x01"                  // mov al,0x1
             . "\xC3"                      // ret
             . "\x30\xC0"                  // xor al,al
             . "\xC3";                     // ret
    }
}
