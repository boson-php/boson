<?php

declare(strict_types=1);

namespace Boson\Internal\Win32;

use FFI\CData;
use FFI\CType;

/**
 * @mixin \FFI
 *
 * @seal-properties
 * @seal-methods
 */
final readonly class LibKernel32
{
    /**
     * @param CType|non-empty-string $type
     */
    public function new(CType|string $type, bool $owned = true, bool $persistent = false): CData {}

    /**
     * @param CType|non-empty-string $type
     */
    public function cast(CType|string $type, CData|int|float|bool|null $ptr): CData {}

    public function FreeConsole(): bool {}
}
