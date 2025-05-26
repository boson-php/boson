<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Family;

use Boson\Component\OsInfo\FamilyInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Component\OsInfo\Family
 */
final readonly class BuiltinFamily implements FamilyInterface
{
    use FamilyImpl;
}
