<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Family;

use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\FamilyInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Component\OsInfo\Family
 */
final readonly class BuiltinFamily implements FamilyInterface
{
    use FamilyImpl;

    public static function tryFrom(string $name): ?BuiltinFamily
    {
        return [
            'windows' => Family::Windows,
            'unix' => Family::Unix,
            'linux' => Family::Linux,
            'bsd' => Family::BSD,
            'solaris' => Family::Solaris,
            'darwin' => Family::Darwin,
        ][\strtolower($name)] ?? null;
    }
}
