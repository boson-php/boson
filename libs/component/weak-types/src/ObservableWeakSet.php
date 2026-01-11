<?php

declare(strict_types=1);

namespace Boson\Component\WeakType;

use Boson\Component\WeakType\Internal\ReferenceReleaseCallback;

/**
 * When adding an object using {@see ObservableWeakSet::watch()} method,
 * this implementation does not increase its refcount.
 *
 * The implementation calls the {@see ObservableWeakSet::watch()} `$onRelease`
 * callback only if there are no references left to the object.
 *
 * @template TEntry of object = object
 *
 * @template-implements \IteratorAggregate<array-key, TEntry>
 * @template-implements ObservableSetInterface<TEntry>
 */
final readonly class ObservableWeakSet implements ObservableSetInterface, \IteratorAggregate
{
    /**
     * @var \WeakMap<TEntry, ReferenceReleaseCallback<TEntry>>
     */
    private \WeakMap $memory;

    public function __construct()
    {
        $this->memory = new \WeakMap();
    }

    /**
     * @param TEntry $entry
     * @param \Closure(TEntry):void $onRelease
     *
     * @return TEntry
     */
    public function watch(object $entry, \Closure $onRelease): object
    {
        $this->memory[$entry] = new ReferenceReleaseCallback($entry, $onRelease);

        return $entry;
    }

    public function detach(object $entry): void
    {
        unset($this->memory[$entry]);
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->memory as $key => $_) {
            yield $key;
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
