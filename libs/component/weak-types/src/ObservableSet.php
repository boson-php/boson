<?php

declare(strict_types=1);

namespace Boson\Component\WeakType;

use Boson\Component\WeakType\Internal\ReferenceReleaseCallback;

/**
 * The implementation calls the {@see ObservableSet::watch()} `$onRelease`
 * callback only if there are no references left to the object.
 *
 * @template TEntry of object = object
 *
 * @template-implements \IteratorAggregate<array-key, TEntry>
 * @template-implements ObservableSetInterface<TEntry>
 */
final readonly class ObservableSet implements ObservableSetInterface, \IteratorAggregate
{
    /**
     * @var \SplObjectStorage<TEntry, void>
     */
    private \SplObjectStorage $references;

    /**
     * @var \WeakMap<TEntry, ReferenceReleaseCallback<TEntry>>
     */
    private \WeakMap $memory;

    public function __construct()
    {
        $this->references = new \SplObjectStorage();
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
        $this->references->offsetSet($entry, null);

        return $entry;
    }

    public function detach(object $entry): void
    {
        unset($this->memory[$entry], $this->references[$entry]);
    }

    public function getIterator(): \Traversable
    {
        return $this->references;
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return $this->memory->count();
    }
}
