<?php

declare(strict_types=1);

namespace Boson\Component\WeakType;

use Boson\Component\WeakType\Internal\ReferenceReleaseCallback;

/**
 * When adding an object using {@see ObservableWeakMap::watch()} method,
 * this implementation does not increase its refcount.
 *
 * @template TObservable of object = object
 * @template TValue of object = object
 *
 * @template-implements \IteratorAggregate<TObservable, TValue>
 * @template-implements ObservableMapInterface<TObservable, TValue>
 */
final readonly class ObservableWeakMap implements
    ObservableMapInterface,
    \IteratorAggregate
{
    /**
     * @var \WeakMap<TObservable, ReferenceReleaseCallback<TValue>>
     */
    private \WeakMap $memory;

    public function __construct()
    {
        $this->memory = new \WeakMap();
    }

    public function watch(object $key, object $value, \Closure $onRelease): object
    {
        $this->memory[$key] = new ReferenceReleaseCallback($value, $onRelease);

        return $key;
    }

    public function find(object $key): ?object
    {
        if (!$this->memory->offsetExists($key)) {
            return null;
        }

        return $this->memory[$key]->reference;
    }

    public function has(object $key): bool
    {
        return $this->memory->offsetExists($key);
    }

    public function detach(object $key): void
    {
        unset($this->memory[$key]);
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->memory as $key => $ref) {
            yield $key => $ref->reference;
        }
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return $this->memory->count();
    }
}
