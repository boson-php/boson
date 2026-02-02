<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidQueryNameArgumentException;
use Boson\Component\Uri\Exception\InvalidQueryValueArgumentException;
use Boson\Contracts\Uri\Component\MutableQueryInterface;

/**
 * @phpstan-consistent-constructor
 */
final class MutableQuery extends Query implements MutableQueryInterface
{
    /**
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     * @throws InvalidQueryValueArgumentException if an invalid query value is provided
     */
    public function putAll(iterable $parameters): void
    {
        $this->parameters = $this->formatParameters($parameters);
    }

    /**
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     * @throws InvalidQueryValueArgumentException if an invalid query value is provided
     */
    public function put(string $name, string|int|float|bool|null|iterable $value = null): void
    {
        $this->setParameter($name, $value);
    }

    /**
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     */
    public function remove(string $name): void
    {
        $this->removeParameter($name);
    }

    /**
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     * @throws InvalidQueryValueArgumentException if an invalid query value is provided
     */
    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!\is_string($offset)) {
            throw InvalidQueryNameArgumentException::becauseComponentMustBe('non-empty-string', $offset);
        }

        $this->setParameter($offset, $value);
    }

    /**
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     */
    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        if (!\is_string($offset)) {
            throw InvalidQueryNameArgumentException::becauseComponentMustBe('non-empty-string', $offset);
        }

        $this->removeParameter($offset);
    }
}
