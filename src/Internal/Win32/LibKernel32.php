<?php

declare(strict_types=1);

namespace Boson\Internal\Win32;

use FFI\Env\Runtime;

/**
 * @mixin \FFI
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson
 */
final readonly class LibKernel32
{
    private \FFI $ffi;

    public function __construct()
    {
        Runtime::assertAvailable();

        $this->ffi = \FFI::cdef(
            code: (string) \file_get_contents(__FILE__, offset: __COMPILER_HALT_OFFSET__),
            lib: 'kernel32.dll',
        );
    }
}

__halt_compiler();

bool FreeConsole(void);
