<?php

declare(strict_types=1);

namespace Boson\Component\Saucer\Loader;

enum DesktopEnvironment
{
    case GTK;
    case QT;
    case Other;

    public static function createFromGlobals(OperatingSystem $os): self
    {
        return match ($os) {
            OperatingSystem::Windows,
            OperatingSystem::MacOS => self::Other,
            default => match (\strtolower($_SERVER['XDG_CURRENT_DESKTOP'] ?? '')) {
                'kde' => self::QT,
                default => self::GTK,
            }
        };
    }
}