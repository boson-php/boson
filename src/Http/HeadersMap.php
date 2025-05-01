<?php

declare(strict_types=1);

namespace Boson\Http;

/**
 * An implementation of immutable headers list.
 *
 * @template-implements \IteratorAggregate<non-empty-lowercase-string, string>
 */
class HeadersMap implements HeadersInterface, \IteratorAggregate
{
    /**
     * @var array<non-empty-lowercase-string, list<string>>
     */
    protected array $lines;

    /**
     * Expects list of header values in format:
     *
     * ```
     * [
     *      'lowercase-header-name' => ['value-1', 'value-2'],
     *      'lowercase-header-name-2' => ['value-1'],
     * ]
     * ```
     *
     * @param iterable<non-empty-lowercase-string, list<string>> $headers
     */
    final public function __construct(iterable $headers = [])
    {
        $this->lines = \iterator_to_array($headers);
    }

    /**
     * Creates a headers object from an iterator as PSR or Symfony-like form
     *
     * ```
     * [
     *      'Header-Name-1' => ['value', 'value2'],
     *      'Header-Name-2' => 'value',
     * ]
     * ```
     *
     * @api
     *
     * @param iterable<non-empty-string, string|iterable<mixed, string>> $headers
     */
    public static function createFromIterable(iterable $headers): static
    {
        if ($headers instanceof self) {
            return new static($headers->lines);
        }

        $formatted = [];

        foreach ($headers as $name => $value) {
            $name = self::getFormattedHeaderName($name);

            if (\is_string($value)) {
                $value = [$value];
            }

            foreach ($value as $item) {
                $formatted[$name][] = $item;
            }
        }

        return new static($formatted);
    }

    /**
     * @api
     */
    public static function createFromHeaders(HeadersInterface $headers): static
    {
        if ($headers instanceof self) {
            return new static($headers->lines);
        }

        return self::createFromIterable($headers);
    }

    /**
     * @phpstan-pure
     *
     * @param non-empty-string $name
     *
     * @return non-empty-lowercase-string
     */
    final public static function getFormattedHeaderName(string $name): string
    {
        return \strtolower($name);
    }

    public function first(string $name, ?string $default = null): ?string
    {
        $formatted = self::getFormattedHeaderName($name);
        $lines = $this->lines;

        if (\array_key_exists($formatted, $lines)) {
            return $lines[$formatted][0] ?? $default;
        }

        return $default;
    }

    public function all(string $name): array
    {
        return $this->lines[self::getFormattedHeaderName($name)]
            ?? [];
    }

    public function has(string $name): bool
    {
        $formatted = self::getFormattedHeaderName($name);

        return \array_key_exists($formatted, $this->lines);
    }

    public function contains(string $name, string $value): bool
    {
        $formatted = self::getFormattedHeaderName($name);

        return \in_array($value, $this->lines[$formatted] ?? [], true);
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->lines as $key => $values) {
            foreach ($values as $value) {
                yield $key => $value;
            }
        }
    }

    public function count(): int
    {
        return \count($this->lines);
    }
}
