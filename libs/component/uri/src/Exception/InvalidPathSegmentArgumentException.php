<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Exception;

/**
 * @phpstan-consistent-constructor
 */
class InvalidPathSegmentArgumentException extends InvalidPathArgumentException
{
    #[\Override]
    public static function getComponentName(): string
    {
        return 'path segment';
    }
}
