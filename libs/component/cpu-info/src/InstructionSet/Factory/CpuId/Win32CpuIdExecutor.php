<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet\Factory\CpuId;

final readonly class Win32CpuIdExecutor implements CpuIdExecutorInterface
{
    private \FFI $ffi;

    public function __construct()
    {
        $this->ffi = \FFI::cdef(\file_get_contents(
            filename: __FILE__,
            offset: __COMPILER_HALT_OFFSET__,
        ), 'kernel32.dll');
    }

    public function execute(\Stringable|string $expr): bool
    {
        $code = (string) $expr;
        $length = \strlen($code);

        // MEM_COMMIT | MEM_RESERVE (0x3000) + PAGE_READWRITE (0x04)
        $memory = $this->ffi->VirtualAlloc(null, $length, 0x3000, 0x04);

        if (\FFI::isNull($memory)) {
            throw new \RuntimeException(\sprintf(
                'VirtualAlloc failed (0x%x)',
                $this->ffi->GetLastError(),
            ));
        }

        \FFI::memcpy($memory, $code, $length);

        $prev = $this->ffi->new('unsigned int');
        // PAGE_EXECUTE_READ (0x20)
        if ($this->ffi->VirtualProtect($memory, $length, 0x20, \FFI::addr($prev)) === false) {
            throw new \RuntimeException(\sprintf(
                'VirtualProtect failed (0x%x)',
                $this->ffi->GetLastError(),
            ));
        }

        $cpuid = $this->ffi->cast('int(*)()', $memory);
        $result = $cpuid();

        // MEM_RELEASE (0x8000)
        if ($this->ffi->VirtualFree($memory, 0, 0x8000) === false) {
            throw new \RuntimeException(\sprintf(
                'VirtualFree failed (0x%x)',
                $this->ffi->GetLastError(),
            ));
        }

        return $result !== 0;
    }
}

__halt_compiler();

typedef void *LPVOID;
typedef size_t SIZE_T;  // typedef ULONG_PTR SIZE_T;
typedef unsigned long DWORD;
typedef DWORD *PDWORD;
typedef bool BOOL;      // typedef int BOOL;

LPVOID VirtualAlloc(
    LPVOID lpAddress,
    SIZE_T dwSize,
    DWORD  flAllocationType,
    DWORD  flProtect
);

BOOL VirtualProtect(
    LPVOID lpAddress,
    SIZE_T dwSize,
    DWORD  flNewProtect,
    PDWORD lpflOldProtect
);

BOOL VirtualFree(
    LPVOID lpAddress,
    SIZE_T dwSize,
    DWORD  dwFreeType
);

DWORD GetLastError();
