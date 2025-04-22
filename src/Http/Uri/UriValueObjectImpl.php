<?php

declare(strict_types=1);

namespace Boson\Http\Uri;

use Boson\Http\UriInterface;

/**
 * @phpstan-require-implements UriInterface
 */
trait UriValueObjectImpl
{
    use UriStringableImpl;

    //
    // Abstract properties not available.
    // @link https://github.com/php/php-src/issues/18391
    //
    // abstract public ?SchemeInterface $scheme { get; }
    // abstract public ?AuthorityInterface $authority { get; }
    // abstract public PathInterface $path { get; }
    // abstract QueryInterface $query { get; }
    // abstract ?string $fragment { get; }
    //

    public function equals(mixed $object): bool
    {
        return $object === $this
            || (
                $object instanceof static
                && $object->fragment === $this->fragment
                && ($object->scheme === $this->scheme
                    || $object->scheme?->equals($this->scheme) === true)
                && ($object->authority === $this->authority
                    || $object->authority?->equals($this->authority) === true)
                && ($object->path === $this->path
                    || $object->path->equals($this->path) === true)
                && ($object->query === $this->query
                    || $object->query->equals($this->query) === true)
            );
    }

    public function toString(): string
    {
        return (string) $this;
    }
}
