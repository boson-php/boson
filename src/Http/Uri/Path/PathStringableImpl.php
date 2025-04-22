<?php

declare(strict_types=1);

namespace Boson\Http\Uri\Path;

use Boson\Http\Uri\PathInterface;

/**
 * @phpstan-require-implements PathInterface
 */
trait PathStringableImpl
{
    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract private array $segments { get; }
    //

    public function __toString(): string
    {
        $result = [];

        foreach ($this->segments as $segment) {
            $result[] = \urlencode($segment);
        }

        return \implode(PathInterface::SEGMENT_DELIMITER, $result);
    }
}
