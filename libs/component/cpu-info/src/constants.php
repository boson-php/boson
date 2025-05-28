<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Architecture {

    const X86 = new BuiltinArchitecture('x86');

    const AMD64 = new BuiltinArchitecture('amd64', X86);

    const ARM = new BuiltinArchitecture('arm');

    const ARM64 = new BuiltinArchitecture('aarch64', ARM);

    const ITANIUM = new BuiltinArchitecture('ia64');

    const RISCV32 = new BuiltinArchitecture('riscv32');

    const RISCV64 = new BuiltinArchitecture('riscv64', RISCV32);

    const MIPS = new BuiltinArchitecture('mips');

    const MIPS64 = new BuiltinArchitecture('mips64', MIPS);

    const PPC = new BuiltinArchitecture('ppc');

    const PPC64 = new BuiltinArchitecture('ppc64', PPC);

    const SPARC = new BuiltinArchitecture('sparc');

    const SPARC64 = new BuiltinArchitecture('sparc64', SPARC);

}

namespace Boson\Component\CpuInfo\InstructionSet {

    use const Boson\Component\CpuInfo\Architecture\AMD64;
    use const Boson\Component\CpuInfo\Architecture\X86;

    const MMX = new BuiltinInstructionSet('mmx', X86);
    const SSE = new BuiltinInstructionSet('sse', X86);
    const SSE2 = new BuiltinInstructionSet('sse2', X86);
    const SSE3 = new BuiltinInstructionSet('sse3', X86);
    const SSSE3 = new BuiltinInstructionSet('ssse3', AMD64);
    const SSE4_1 = new BuiltinInstructionSet('sse4.1', AMD64);
    const SSE4_2 = new BuiltinInstructionSet('sse4.2', AMD64);
    const FMA3 = new BuiltinInstructionSet('fma3', AMD64);
    const AVX = new BuiltinInstructionSet('avx', AMD64);
    const AVX2 = new BuiltinInstructionSet('avx2', AMD64);
    const AVX512 = new BuiltinInstructionSet('avx512', AMD64);

}
