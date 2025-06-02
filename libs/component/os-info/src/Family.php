<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo;

use Boson\Component\OsInfo\Family\BuiltinFamily;
use Boson\Component\OsInfo\Family\Factory\DefaultFamilyFactory;
use Boson\Component\OsInfo\Family\Factory\InMemoryFamilyFactory;
use Boson\Component\OsInfo\Family\FamilyImpl;

require_once __DIR__ . '/Family/constants.php';

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
     * @api
     */
    public static function createFromGlobals(): FamilyInterface
    {
        /** @phpstan-var InMemoryFamilyFactory $factory */
        static $factory = new InMemoryFamilyFactory(
            delegate: new DefaultFamilyFactory(),
        );

        return $factory->createFamily();
    }

    /**
     * @api
     *
     * @param non-empty-string $name
     */
    public static function tryFrom(string $name): ?BuiltinFamily
    {
        return BuiltinFamily::tryFrom($name);
    }

    /**
     * @api
     *
     * @param non-empty-string $name
     */
    public static function from(string $name): FamilyInterface
    {
        return self::tryFrom($name) ?? new self($name);
    }

    /**
     * @api
     *
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
