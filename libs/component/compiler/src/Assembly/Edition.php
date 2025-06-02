<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Assembly;

use Boson\Component\Compiler\Assembly\Edition\BuiltinEdition;
use Boson\Component\Compiler\Assembly\Edition\EditionImpl;

require_once __DIR__ . '/Edition/constants.php';

final readonly class Edition implements EditionInterface
{
    use EditionImpl;

    /**
     * Minimal PHP edition.
     *
     * Contains only essential and some lightweight extensions.
     */
    public const EditionInterface Minimal = Edition\MINIMAL;

    /**
     * Standard PHP edition.
     *
     * Contains all the most popular extensions (except mysql and
     * postgres PDO drivers).
     */
    public const EditionInterface Standard = Edition\STANDARD;

    /**
     * @api
     *
     * @param non-empty-string $name
     */
    public static function tryFrom(string $name): ?BuiltinEdition
    {
        return BuiltinEdition::tryFrom($name);
    }

    /**
     * @api
     *
     * @param non-empty-string $name
     */
    public static function from(string $name): EditionInterface
    {
        return self::tryFrom($name) ?? new self($name);
    }

    /**
     * @api
     *
     * @return non-empty-list<EditionInterface>
     */
    public static function cases(): array
    {
        /** @var non-empty-array<non-empty-string, EditionInterface> $cases */
        static $cases = new \ReflectionClass(self::class)
            ->getConstants();

        /** @var non-empty-list<EditionInterface> */
        return \array_values($cases);
    }
}
