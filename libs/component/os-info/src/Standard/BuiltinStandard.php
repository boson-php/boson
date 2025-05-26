<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Standard;

use Boson\Component\OsInfo\StandardInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Component\OsInfo\Standard
 */
final readonly class BuiltinStandard implements StandardInterface
{
    use StandardImpl;
}
