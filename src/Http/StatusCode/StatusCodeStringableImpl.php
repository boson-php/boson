<?php

declare(strict_types=1);

namespace Boson\Http\StatusCode;

use Boson\Http\StatusCodeInterface;

/**
 * @phpstan-require-implements StatusCodeInterface
 */
trait StatusCodeStringableImpl
{
    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract public int $code { get; }
    // abstract public string $reason { get; }
    //

    public function __toString(): string
    {
        $result = (string) $this->code;

        if ($this->reason === '') {
            $result .= ' ' . $this->reason;
        }

        return $result;
    }
}
