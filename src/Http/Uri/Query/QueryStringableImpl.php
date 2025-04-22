<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Query;

use Boson\Http\Uri\QueryInterface;

/**
 * @phpstan-require-implements QueryInterface
 */
trait QueryStringableImpl
{
    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract private array $components { get; }
    //

    public function __toString(): string
    {
        $result = [];

        foreach ($this as $key => $value) {
            $result[] = \urlencode($key)
                . QueryInterface::KV_DELIMITER
                . \urlencode($value);
        }

        return \implode(QueryInterface::SEGMENT_DELIMITER, $result);
    }
}
