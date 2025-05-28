<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet\Factory\CpuId;

use FFI\CData;

final readonly class Win32CpuIdExecutor implements CpuIdExecutorInterface
{
    private \FFI $ffi;

    public function __construct()
    {
        $this->ffi = \FFI::cdef((string) @\file_get_contents(
            filename: __FILE__,
            offset: __COMPILER_HALT_OFFSET__,
        ), 'kernel32.dll');
    }

    public function execute(\Stringable|string $expr): bool
    {
        $code = (string) $expr;
        $length = \strlen($code);

        // MEM_COMMIT | MEM_RESERVE (0x3000) + PAGE_READWRITE (0x04)
        /**
         * @var CData $memory
         *
         * @phpstan-ignore-next-line
         */
        $memory = $this->ffi->VirtualAlloc(null, $length, 0x3000, 0x04);

        if (\FFI::isNull($memory)) {
            throw new \RuntimeException(\sprintf(
                'VirtualAlloc failed (0x%x)',
                /** @phpstan-ignore-next-line */
                $this->ffi->GetLastError(),
            ));
        }

        \FFI::memcpy($memory, $code, $length);

        /** @phpstan-ignore-next-line */
        $prev = $this->ffi->new('unsigned int');

        // PAGE_EXECUTE_READ (0x20)
        /** @phpstan-ignore-next-line */
        if ($this->ffi->VirtualProtect($memory, $length, 0x20, \FFI::addr($prev)) === false) {
            throw new \RuntimeException(\sprintf(
                'VirtualProtect failed (0x%x)',
                /** @phpstan-ignore-next-line */
                $this->ffi->GetLastError(),
            ));
        }

        /**
         * @var callable():int $cpuid
         *
         * @phpstan-ignore-next-line
         */
        $cpuid = $this->ffi->cast('int(*)()', $memory);
        $result = $cpuid();

        // MEM_RELEASE (0x8000)
        /** @phpstan-ignore-next-line */
        if ($this->ffi->VirtualFree($memory, 0, 0x8000) === false) {
            throw new \RuntimeException(\sprintf(
                'VirtualFree failed (0x%x)',
                /** @phpstan-ignore-next-line */
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
