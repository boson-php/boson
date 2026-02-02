<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidPathIndexArgumentException;
use Boson\Component\Uri\Exception\InvalidPathSegmentArgumentException;
use Boson\Contracts\Uri\Component\MutablePathInterface;

/**
 * @phpstan-consistent-constructor
 */
final class MutablePath extends Path implements
    MutablePathInterface
{
    /**
     * The {@see $isAbsolute} value is both readable and writable.
     */
    public bool $isAbsolute = true;

    /**
     * The {@see $hasTrailingSlash} value is both readable and writable.
     */
    public bool $hasTrailingSlash = false;

    /**
     * @throws InvalidPathSegmentArgumentException if an invalid path segment is provided
     */
    public function putAll(iterable $segments): void
    {
        $this->segments = $this->formatSegments($segments);
    }

    /**
     * @throws InvalidPathSegmentArgumentException if an invalid path segment is provided
     * @throws InvalidPathIndexArgumentException if an invalid path index is provided
     */
    public function put(\Stringable|string $segment, ?int $index = null): void
    {
        $this->setSegment($segment, $index);
    }

    /**
     * @throws InvalidPathIndexArgumentException if an invalid path index is provided
     */
    public function remove(int $index): void
    {
        $this->removeSegment($index);
    }

    /**
     * @throws InvalidPathSegmentArgumentException if an invalid path segment is provided
     * @throws InvalidPathIndexArgumentException if an invalid path index is provided
     */
    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!\is_int($offset) && $offset !== null) {
            throw InvalidPathIndexArgumentException::becauseComponentMustBe('int<0, max>|null', $offset);
        }

        if (!\is_string($value) && !$value instanceof \Stringable) {
            throw InvalidPathSegmentArgumentException::becauseComponentMustBe('\Stringable|string', $value);
        }

        $this->setSegment($value, $offset);
    }

    /**
     * @throws InvalidPathIndexArgumentException if an invalid path index is provided
     */
    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        if (!\is_int($offset) && $offset !== null) {
            throw InvalidPathIndexArgumentException::becauseComponentMustBe('int<0, max>', $offset);
        }

        $this->removeSegment($offset);
    }
}
