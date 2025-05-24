<?php

declare(strict_types=1);

namespace Boson\Internal;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson
 */
final readonly class DebugEnvResolver
{
    public static function resolve(?bool $value): bool
    {
        return $value ?? self::isDebugEnabledFromEnvironment();
    }

    /**
     * Gets debug value from environment's "zend.assertions" status
     */
    private static function isDebugEnabledFromEnvironment(): bool
    {
        $debug = false;

        /**
         * Enable debug mode if "zend.assertions" is 1.
         *
         * @link https://www.php.net/manual/en/function.assert.php
         */
        assert($debug = true);

        return $debug;
    }
}
