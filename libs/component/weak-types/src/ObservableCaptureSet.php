<?php

declare(strict_types=1);

namespace Boson\Component\WeakType;

use Boson\Component\WeakType\Internal\ReferenceReleaseCallback;

/**
 * The implementation calls the {@see ObservableCaptureSet::watch()} `$onRelease`
 * callback only if {@see ObservableCaptureSet} does not references to the object.
 *
 * @template TEntry of object = object
 *
 * @template-implements \IteratorAggregate<array-key, TEntry>
 * @template-implements ObservableSetInterface<TEntry>
 */
final readonly class ObservableCaptureSet implements ObservableSetInterface, \IteratorAggregate
{
    /**
     * @var \SplObjectStorage<TEntry, ReferenceReleaseCallback<TEntry>>
     */
    private \SplObjectStorage $memory;

    public function __construct()
    {
        $this->memory = new \SplObjectStorage();
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
        foreach ($this->memory as $entry) {
            yield $entry;
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
