<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo;

use Boson\Component\OsInfo\Family\FamilyImpl;

/**
 * Representing predefined operating system families.
 */
final readonly class Family implements FamilyInterface
{
    use FamilyImpl;

    /**
     * Represents the Windows family of operating systems.
     */
    public const FamilyInterface Windows = Family\WINDOWS;

    /**
     * Represents the Linux family of operating systems.
     */
    public const FamilyInterface Linux = Family\LINUX;

    /**
     * Represents the Unix family of operating systems.
     */
    public const FamilyInterface Unix = Family\UNIX;

    /**
     * BSD operating system family.
     */
    public const FamilyInterface BSD = Family\BSD;

    /**
     * Solaris operating system family.
     */
    public const FamilyInterface Solaris = Family\SOLARIS;

    /**
     * Darwin operating system family.
     */
    public const FamilyInterface Darwin = Family\DARWIN;

    /**
     * @return non-empty-list<FamilyInterface>
     */
    public static function cases(): array
    {
        /** @var non-empty-array<non-empty-string, FamilyInterface> $cases */
        static $cases = new \ReflectionClass(self::class)
            ->getConstants();

        /** @var non-empty-list<FamilyInterface> */
        return \array_values($cases);
    }
}
