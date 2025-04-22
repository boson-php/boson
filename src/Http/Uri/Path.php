<?php

declare(strict_types=1);

namespace Boson\Http\Uri;

use Boson\Http\Uri\Path\PathValueObjectImpl;

/**
 * @template-implements \IteratorAggregate<array-key, non-empty-string>
 */
final readonly class Path implements PathInterface, \IteratorAggregate
{
    use PathValueObjectImpl;

    /**
     * @var list<non-empty-string>
     */
    private array $segments;

    /**
     * @param iterable<mixed, non-empty-string> $segments
     */
    public function __construct(iterable $segments = [])
    {
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
}
