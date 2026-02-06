<?php

declare(strict_types=1);

namespace Boson\Component\Ipc\Exception;

class DriverNotAvailableException extends DriverException
{
    final public const int ERROR_CODE_MISSING_DRIVER = 0x01;
    final public const int ERROR_CODE_MISSING_EXTENSION = 0x02;
    final public const int ERROR_CODE_MISSING_PACKAGE = 0x03;

    public static function becauseNoSuitableDriver(?\Throwable $prev = null): self
    {
        return new self('No suitable shared memory driver available', self::ERROR_CODE_MISSING_DRIVER, $prev);
    }

    public static function becauseExtensionRequired(string $extension, ?\Throwable $prev = null): self
    {
        $template = 'The "%s" PHP extension is required';

        return new self(\sprintf($template, $extension), self::ERROR_CODE_MISSING_EXTENSION, $prev);
    }

    public static function becausePackageRequired(string $package, ?\Throwable $prev = null): self
    {
        $template = 'The "%s" package is required. Try running the "composer require %1$s"';

        return new self(\sprintf($template, $package), self::ERROR_CODE_MISSING_PACKAGE, $prev);
    }
}
