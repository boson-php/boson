<?php

declare(strict_types=1);

namespace Boson\WebView\Internal\WebViewCreateInfo;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView
 */
final readonly class StorageDirectoryResolver
{
    /**
     * @var non-empty-string
     */
    final protected const string DEFAULT_STORAGE_DIRECTORY_NAME = '.boson';

    /**
     * @return non-empty-string|false
     */
    public static function resolve(string|false|null $storage): string|false
    {
        if ($storage === null || $storage === '') {
            return self::getDefaultStorageDirectory();
        }

        return $storage;
    }

    /**
     * @return non-empty-string
     */
    private static function getDefaultStorageDirectory(): string
    {
        $directory = self::getCurrentWorkingDirectory();

        return \vsprintf('%s/%s', [
            $directory,
            self::DEFAULT_STORAGE_DIRECTORY_NAME,
        ]);
    }

    /**
     * @return non-empty-string
     */
    private static function getCurrentWorkingDirectory(): string
    {
        $cwd = match (true) {
            ($cwd = \getcwd()) !== false => $cwd,
            isset($_SERVER['SCRIPT_FILENAME']) && \is_string($_SERVER['SCRIPT_FILENAME'])
                => \dirname($_SERVER['SCRIPT_FILENAME']),
            isset($_SERVER['SCRIPT_NAME']) && \is_string($_SERVER['SCRIPT_NAME'])
                => \dirname($_SERVER['SCRIPT_NAME']),
            isset($_SERVER['PHP_SELF']) && \is_string($_SERVER['PHP_SELF'])
                => \dirname($_SERVER['PHP_SELF']),
            isset($_SERVER['PATH_TRANSLATED']) && \is_string($_SERVER['PATH_TRANSLATED'])
                => \dirname($_SERVER['PATH_TRANSLATED']),
            default => '.',
        };

        /** @var non-empty-string */
        return $cwd === '' ? '.' : $cwd;
    }
}
