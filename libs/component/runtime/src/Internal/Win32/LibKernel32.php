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

    /**
     * @param non-empty-string $method
     * @param array<non-empty-string|int<0, max>, mixed> $args
     */
    public function __call(string $method, array $args): mixed
    {
        assert($method !== '', 'Method name MUST not be empty');

        return $this->ffi->$method(...$args);
    }

    public function __serialize(): array
    {
        throw new \LogicException('Cannot serialize library');
    }

    public function __clone()
    {
        throw new \LogicException('Cannot clone library');
    }
}

__halt_compiler();

bool FreeConsole(void);
