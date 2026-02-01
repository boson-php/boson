<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidPathIndexArgumentException;
use Boson\Component\Uri\Exception\InvalidPathSegmentArgumentException;
use Boson\Contracts\Uri\Component\PathInterface;

/**
 * @template-implements \IteratorAggregate<array-key, non-empty-string>
 *
 * @phpstan-sealed MutablePath
 */
class Path implements PathInterface, \IteratorAggregate
{
    /**
     * @var list<non-empty-string>
     */
    public protected(set) array $segments;

    /**
     * @var list<non-empty-string>
     */
    private array $encoded {
        get {
            $segments = [];

            foreach ($this->segments as $segment) {
                $segments[] = \rawurlencode($segment);
            }

            return $segments;
        }
    }

    public bool $isEmpty {
        get => $this->segments === [];
    }

    public string $absolute {
        get => '/' . $this->relative;
    }

    public string $relative {
        get => \implode('/', $this->encoded);
    }

    /**
     * @param iterable<mixed, \Stringable|string> $segments
     *
     * @throws InvalidPathSegmentArgumentException if an invalid path segment is provided
     */
    public function __construct(
        iterable $segments = [],
        public protected(set) bool $isAbsolute = true,
        public protected(set) bool $hasTrailingSlash = false,
    ) {
        $this->segments = $this->formatSegmentsArgument($segments);
    }

    /**
     * @throws InvalidPathSegmentArgumentException if an invalid path segment is provided
     */
    public function withSegments(iterable $segments): static
    {
        $self = clone $this;
        $self->segments = $this->formatSegmentsArgument($segments);

        return $self;
    }

    /**
     * @throws InvalidPathSegmentArgumentException if an invalid path segment is provided
     * @throws InvalidPathIndexArgumentException if an invalid path index is provided
     */
    public function withSegment(\Stringable|string $segment, ?int $index = null): static
    {
        $self = clone $this;
        $self->setSegment($segment, $index);

        return $self;
    }

    /**
     * @throws InvalidPathIndexArgumentException if an invalid path index is provided
     */
    public function withoutSegment(int $index): static
    {
        $self = clone $this;
        $self->removeSegment($index);

        return $self;
    }

    /**
     * @throws InvalidPathIndexArgumentException if an invalid path index is provided
     */
    final public function at(int $index): ?string
    {
        if ($index < 0) {
            throw InvalidPathIndexArgumentException::becauseComponentMustBe('int<0, max>', $index);
        }

        return $this->segments[$index] ?? null;
    }

    /**
     * @throws InvalidPathSegmentArgumentException if an invalid path segment is provided
     */
    final public function contains(\Stringable|string $segment): bool
    {
        return \in_array($this->formatSegmentArgument($segment), $this->segments, true);
    }

    /**
     * Format segments collection argument
     *
     * @param iterable<mixed, \Stringable|string> $segments
     * @return list<non-empty-string>
     *
     * @throws InvalidPathSegmentArgumentException if an invalid path segment is provided
     */
    protected function formatSegmentsArgument(iterable $segments): array
    {
        $result = [];

        foreach ($segments as $segment) {
            $result[] = $this->formatSegmentArgument($segment);
        }

        return $result;
    }

    /**
     * @param int<0, max> $index
     * @throws InvalidPathIndexArgumentException if an invalid path index is provided
     */
    protected function removeSegment(int $index): void
    {
        if ($index < 0) {
            throw InvalidPathIndexArgumentException::becauseComponentMustBe('int<0, max>', $index);
        }

        if ($index >= \count($this->segments)) {
            return;
        }

        $segments = $this->segments;

        unset($segments[$index]);

        $this->segments = \array_values($segments);
    }

    /**
     * @param int<0, max>|null $index
     * @throws InvalidPathSegmentArgumentException if an invalid path segment is provided
     * @throws InvalidPathIndexArgumentException if an invalid path index is provided
     */
    protected function setSegment(\Stringable|string $segment, ?int $index = null): void
    {
        if ($index === null || $index >= \count($this->segments)) {
            $this->segments[] = $this->formatSegmentArgument($segment);

            return;
        }

        if ($index < 0) {
            throw InvalidPathIndexArgumentException::becauseComponentMustBe('int<0, max>', $index);
        }

        $this->segments[$index] = $this->formatSegmentArgument($segment);
    }

    /**
     * Format segment argument
     *
     * @return non-empty-string
     * @throws InvalidPathSegmentArgumentException if an invalid path segment is provided
     */
    protected function formatSegmentArgument(string|\Stringable $segment): string
    {
        if ($segment instanceof \Stringable) {
            try {
                $segment = (string) $segment;
                /** @phpstan-ignore-next-line : This is not a dead catch */
            } catch (\Throwable $e) {
                throw InvalidPathSegmentArgumentException::becauseStringableErrorOccurs($e);
            }
        }

        if ($segment === '') {
            throw InvalidPathSegmentArgumentException::becauseComponentIsEmpty();
        }

        return $segment;
    }

    /**
     * @throws InvalidPathIndexArgumentException if an invalid path index is provided
     */
    public function offsetExists(mixed $offset): bool
    {
        if (!\is_int($offset) || $offset < 0) {
            throw InvalidPathIndexArgumentException::becauseComponentMustBe('int<0, max>', $offset);
        }

        return isset($this->segments[$offset]);
    }

    /**
     * @throws InvalidPathIndexArgumentException if an invalid path index is provided
     */
    public function offsetGet(mixed $offset): ?string
    {
        if (!\is_int($offset) || $offset < 0) {
            throw InvalidPathIndexArgumentException::becauseComponentMustBe('int<0, max>', $offset);
        }

        return $this->segments[$offset] ?? null;
    }

    #[\Deprecated('Data mutation in an immutable context is not allowed')]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \BadMethodCallException('Cannot modify value of immutable path ' . static::class);
    }

    #[\Deprecated('Data mutation in an immutable context is not allowed')]
    public function offsetUnset(mixed $offset): void
    {
        throw new \BadMethodCallException('Cannot remove value of immutable path ' . static::class);
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
        $path = \implode('/', $this->encoded);

        if ($this->isAbsolute) {
            $path = '/' . $path;
        }

        if ($this->segments !== [] && $this->hasTrailingSlash) {
            $path .= '/';
        }

        return $path;
    }
}
