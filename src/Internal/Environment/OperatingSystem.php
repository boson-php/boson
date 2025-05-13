<?php

declare(strict_types=1);

namespace Boson\Internal\Environment;

enum OperatingSystem: string
{
    case Windows = 'windows';
    case Linux = 'linux';
    case BSD = 'bsd';
    case MacOS = 'macos';

    public static function current(): ?self
    {
        static $current = match (\PHP_OS_FAMILY) {
            'Windows' => self::Windows,
            'Linux' => self::Linux,
            'BSD' => self::BSD,
            'Darwin' => self::MacOS,
            default => null,
        };

        /** @var self|null */
        return $current;
    }
}
