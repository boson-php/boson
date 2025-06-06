<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Assembly;

enum AssemblyArchitecture: string
{
    case Amd64 = 'amd64';
    case Arm64 = 'aarch64';

    /**
     * @api
     *
     * @param non-empty-string $name
     */
    private static function normalize(string $name): string
    {
        return match (\strtolower($name)) {
            'x86_64', 'x64' => self::Amd64->value,
            'arm64', 'arm64ilp32' => self::Arm64->value,
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
