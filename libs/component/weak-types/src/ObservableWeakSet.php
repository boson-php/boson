<?php

declare(strict_types=1);

namespace Boson\Component\WeakType;

use Boson\Component\WeakType\Internal\ReferenceReleaseCallback;

/**
 * Allows to store a set of objects and track their
 * destruction (react to GC cleanup).
 *
 * ```
 * // ObservableWeakSet<ExampleObject>
 * $set = new ObservableWeakSet();
 *
 * $set->watch($object, function (ExampleObject $ref) {
 *      echo vsprintf('ExampleObject(%d) has been destroyed', [
 *          get_object_id($ref),
 *      ]);
 * ));
 * ```
 *
 * @template TEntry of object = object
 *
 * @template-implements \IteratorAggregate<array-key, TEntry>
 */
final readonly class ObservableWeakSet implements \IteratorAggregate, \Countable
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
