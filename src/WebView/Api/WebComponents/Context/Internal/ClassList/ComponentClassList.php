<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context\Internal\ClassList;

use Boson\WebView\Api\Data\SyncDataRetrieverInterface;
use Boson\WebView\Api\WebComponents\Context\ClassListInterface;

/**
 * @template-implements \IteratorAggregate<mixed, non-empty-string>
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Api\WebComponents
 */
readonly class ComponentClassList implements ClassListInterface, \IteratorAggregate
{
    public function __construct(
        protected SyncDataRetrieverInterface $data,
    ) {}

    /**
     * @param non-empty-string $class
     *
     * @return non-empty-string
     */
    protected function classToArgument(string $class): string
    {
        return \sprintf('`%s`', \addcslashes($class, '`'));
    }

    public function contains(string $class): bool
    {
        return (bool) $this->data->get(\sprintf(
            'this.classList.contains(%s)',
            $this->classToArgument($class),
        ));
    }

    public function findByIndex(int $index): ?string
    {
        /** @var non-empty-string|null */
        return $this->data->get(\sprintf(
            'this.classList.item(%d)',
            $index,
        ));
    }

    public function count(): int
    {
        /** @var int<0, max> */
        return $this->data->get('this.classList.length');
    }

    public function getIterator(): \Traversable
    {
        /** @var list<non-empty-string> $result */
        $result = $this->data->get('[...this.classList]');

        return new \ArrayIterator($result);
    }

    public function __toString(): string
    {
        /** @var string */
        return $this->data->get('this.classList.toString()');
    }
}
