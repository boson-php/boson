<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Exception;

use Boson\Contracts\Uri\Exception\UriExceptionInterface;

class UriException extends \RuntimeException implements UriExceptionInterface
{
    final protected static function getType(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }

        if (\is_scalar($value)) {
            return \sprintf('%s(%s)', \get_debug_type($value), \var_export($value, true));
        }

        /** @var non-empty-list<string> $parts */
        $parts = \explode("\0", \get_debug_type($value), 1);

        return $parts[0];
    }
}
