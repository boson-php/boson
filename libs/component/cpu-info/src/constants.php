<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Architecture {

    const X86 = new BuiltinArchitecture('x86');

    const AMD64 = new BuiltinArchitecture('amd64');

    const ARM = new BuiltinArchitecture('arm');

    const ARM64 = new BuiltinArchitecture('aarch64');

    const ITANIUM = new BuiltinArchitecture('ia64');

    const RISCV32 = new BuiltinArchitecture('riscv32');

    const RISCV64 = new BuiltinArchitecture('riscv64');

    const MIPS = new BuiltinArchitecture('mips');

    const MIPS64 = new BuiltinArchitecture('mips64');

    const PPC = new BuiltinArchitecture('ppc');

    const PPC64 = new BuiltinArchitecture('ppc64');

    const SPARC = new BuiltinArchitecture('sparc');

    const SPARC64 = new BuiltinArchitecture('sparc64');

}
