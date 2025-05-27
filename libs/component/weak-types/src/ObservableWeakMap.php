<?php

declare(strict_types=1);

namespace Boson\Component\WeakType;

use Boson\Component\WeakType\Internal\ReferenceReleaseCallback;

/**
 * Allows to store a set of objects with referenced values and
 * track their destruction (react to GC cleanup).
 *
 * ```
 * // ObservableWeakMap<ExampleId, CData>
 * $map = new ObservableWeakMap();
 *
 * $map->watch($id, $data, function (CData $ref) {
 *     echo vsprintf('ID has been destroyed, something can be done with its reference %s(%d)', [
 *         $ref::class,
 *         get_object_id($ref),
 *     ]);
 * ));
 * ```
 *
 * @api
 *
 * @template TKey of object = object
 * @template TValue of object = object
 *
 * @template-implements \IteratorAggregate<TKey, TValue>
 */
final readonly class ObservableWeakMap implements \IteratorAggregate, \Countable
{
    /**
     * @var \WeakMap<TKey, ReferenceReleaseCallback<TValue>>
     */
    private \WeakMap $memory;

    public function __construct()
    {
        $this->memory = new \WeakMap();
    }

    /**
     * @param TKey $key
     * @param TValue $value
     * @param \Closure(TValue):void $onRelease
     *
     * @return TKey
     */
    public function watch(object $key, object $value, \Closure $onRelease): object
    {
        $this->memory[$key] = new ReferenceReleaseCallback($value, $onRelease);

        return $key;
    }

    /**
     * @param TKey $key
     *
     * @return TValue|null
     */
    public function find(object $key): ?object
    {
        if (!$this->memory->offsetExists($key)) {
            return null;
        }

        return $this->memory[$key]->reference;
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
