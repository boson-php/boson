<?php

declare(strict_types=1);

namespace Boson\Http\StatusCode;

use Boson\Http\StatusCodeInterface;

/**
 * @phpstan-require-implements StatusCodeInterface
 */
trait StatusCodeValueObjectImpl
{
    use StatusCodeStringableImpl;

    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract public int $code { get; }
    // abstract public string $reason { get; }
    //

    public function toInteger(): int
    {
        return $this->code;
    }

    public function toString(): string
    {
        return $this->reason;
    }

    public function equals(mixed $object): bool
    {
        return $object === $this
            || ($object instanceof static
                && $object->reason === $this->reason
                && $object->code === $this->code);
    }
}
