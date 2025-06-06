<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Assembly;

enum AssemblyEdition: string
{
    /**
     * Minimal PHP edition.
     *
     * Contains only essential and some lightweight extensions.
     */
    case Minimal = 'minimal';

    /**
     * Standard PHP edition.
     *
     * Contains all the most popular extensions (except mysql and
     * postgres PDO drivers).
     */
    case Standard = 'standard';

    /**
     * @api
     *
     * @param non-empty-string $name
     */
    private static function normalize(string $name): string
    {
        return match (\strtolower($name)) {
            'min' => self::Minimal->value,
            'std', 'default' => self::Standard->value,
            default => $name,
        };
    }

    /**
     * @api
     *
     * @param non-empty-string $name
     */
    public static function tryFromNormalized(string $name): ?self
    {
        return self::tryFrom(self::normalize($name));
    }

    /**
     * @api
     *
     * @param non-empty-string $name
     */
    public static function fromNormalized(string $name): self
    {
        return self::from(self::normalize($name));
    }
}
