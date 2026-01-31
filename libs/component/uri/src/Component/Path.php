<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Contracts\Uri\Component\PathInterface;

/**
 * @template-implements \IteratorAggregate<array-key, non-empty-string>
 */
final class Path implements PathInterface, \IteratorAggregate
{
    /**
     * @var list<non-empty-string>
     */
    private readonly array $segments;

    /**
     * @var list<non-empty-string>
     */
    private array $encoded {
        get {
            if (!isset($this->encoded)) {
                $segments = [];

                foreach ($this->segments as $segment) {
                    $segments[] = \rawurlencode($segment);
                }

                $this->encoded = $segments;
            }

            return $this->encoded;
        }
    }

    public string $absolute {
        get => '/' . $this->relative;
    }

    public string $relative {
        get => \implode('/', $this->encoded);
    }

    /**
     * @param iterable<mixed, non-empty-string> $segments
     */
    public function __construct(
        iterable $segments = [],
        public protected(set) bool $isAbsolute = true,
        public protected(set) bool $hasTrailingSlash = false,
    ) {
        $this->segments = \iterator_to_array($segments, false);
    }

    public function at(int $index): ?string
    {
        return $this->segments[$index] ?? null;
    }

    public function contains(\Stringable|string $segment): bool
    {
        $decoded = \rawurldecode((string) $segment);

        return \in_array($decoded, $this->segments, true);
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
                && $other->segments === $this->segments)
            || ($other instanceof PathInterface
                && $other->relative === $this->relative);
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
