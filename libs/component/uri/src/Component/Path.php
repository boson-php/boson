<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Contracts\Uri\Component\PathInterface;

/**
 * @template-implements \IteratorAggregate<array-key, non-empty-string>
 */
final readonly class Path implements PathInterface, \IteratorAggregate
{
    /**
     * @var list<non-empty-string>
     */
    private array $segments;

    /**
     * @param iterable<mixed, non-empty-string> $segments
     */
    public function __construct(
        iterable $segments = [],
        public bool $isAbsolute = true,
        public bool $hasTrailingSlash = false,
    ) {
        $this->segments = \iterator_to_array($segments, false);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->segments);
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return \count($this->segments);
    }

    public function equals(mixed $other): bool
    {
        return $other === $this
            || ($other instanceof self
                && $other->segments === $this->segments);
    }

    public function toString(): string
    {
        return (string) $this;
    }

    public function __toString(): string
    {
        $segments = [];

        foreach ($this->segments as $segment) {
            $segments[] = \rawurlencode($segment);
        }

        $path = \implode('/', $segments);

        if ($this->isAbsolute) {
            $path = '/' . $path;
        }

        if ($segments !== [] && $this->hasTrailingSlash) {
            $path .= '/';
        }

        return $path;
    }
}
