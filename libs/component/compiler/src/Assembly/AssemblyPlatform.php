<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Assembly;

enum AssemblyPlatform: string
{
    case Windows = 'windows';
    case Linux = 'linux';
    case MacOS = 'macos';

    /**
     * @api
     *
     * @param non-empty-string $name
     */
    private static function normalize(string $name): string
    {
        return match (\strtolower($name)) {
            'win32', 'win64', 'win' => self::Windows->value,
            'nix' => self::Linux->value,
            'darwin' => self::MacOS->value,
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
