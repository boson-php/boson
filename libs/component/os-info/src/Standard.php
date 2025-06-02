<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo;

use Boson\Component\OsInfo\Standard\BuiltinStandard;
use Boson\Component\OsInfo\Standard\StandardImpl;

require_once __DIR__ . '/Standard/constants.php';

/**
 * Representing predefined operating system standards.
 */
final readonly class Standard implements StandardInterface
{
    use StandardImpl;

    /**
     * POSIX operating system standard.
     *
     * @link https://posix.opengroup.org/
     * @link https://standards.ieee.org/ieee/1003.1/7700/
     * @link https://www.iso.org/standard/50516.html
     */
    public const StandardInterface Posix = Standard\POSIX;

    /**
     * @api
     */
    public static function tryFrom(string $name): ?BuiltinStandard
    {
        return BuiltinStandard::tryFrom($name);
    }

    /**
     * @api
     */
    public static function from(string $name): StandardInterface
    {
        return self::tryFrom($name) ?? new self($name);
    }

    /**
     * @api
     * @return non-empty-list<StandardInterface>
     */
    public static function cases(): array
    {
        /** @var non-empty-array<non-empty-string, StandardInterface> $cases */
        static $cases = new \ReflectionClass(self::class)
            ->getConstants();

        /** @var non-empty-list<StandardInterface> */
        return \array_values($cases);
    }
}
