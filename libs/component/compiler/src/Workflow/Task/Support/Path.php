<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Workflow\Task\Support;

use Boson\Component\Compiler\Configuration;

final readonly class Path
{
    /**
     * @return ($path is non-empty-string ? non-empty-string : string)
     */
    public static function normalize(string $path): string
    {
        $result = \str_replace(['\\', '/'], \DIRECTORY_SEPARATOR, $path);

        /** @phpstan-ignore ternary.shortNotAllowed */
        return \realpath($result) ?: $result;
    }

    public static function simplify(Configuration $config, string $path): string
    {
        $root = self::normalize($config->root);
        $path = self::normalize($path);

        if (\str_starts_with($path, $root)) {
            $path = \substr($path, \strlen($root));

            if (\str_starts_with($path, \DIRECTORY_SEPARATOR) || $path === '') {
                return '.' . $path;
            }

            return $path;
        }

        return $path;
    }

    /**
     * @return iterable<\RecursiveDirectoryIterator>
     */
    public static function files(string $directory): iterable
    {
        return new \RecursiveIteratorIterator(
            iterator: new \RecursiveDirectoryIterator(
                directory: $directory,
                flags: \FilesystemIterator::SKIP_DOTS,
            ),
            mode: \RecursiveIteratorIterator::CHILD_FIRST,
        );
    }
}
