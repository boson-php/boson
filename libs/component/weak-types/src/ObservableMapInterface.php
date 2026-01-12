<?php

declare(strict_types=1);

namespace Boson\Component\WeakType;

/**
 * Allows to store a set of objects with referenced values and track their
 * destruction (react to GC cleanup).
 *
 * ```
 * $map->watch($id, $ref, function (object $ref): void {
 *     echo vsprintf('ID has been destroyed, something can be done with its reference %s(%d)', [
 *         $ref::class,
 *         spl_object_id($ref),
 *     ]);
 * ));
 * ```
 *
 * @template TObservable of object = object
 * @template TValue of object = object
 *
 * @template-extends \Traversable<TObservable, TValue>
 */
interface ObservableMapInterface extends \Countable, \Traversable
{
    /**
     * @param TObservable $key
     * @param TValue $value
     * @param \Closure(TValue):void $onRelease
     *
     * @return TObservable
     */
    public function watch(object $key, object $value, \Closure $onRelease): object;

    /**
     * @param TObservable $key
     *
     * @return TValue|null
     */
    public function find(object $key): ?object;

    /**
     * @param TObservable $key
     */
    public function has(object $key): bool;

    /**
     * @param TObservable $key
     */
    public function detach(object $key): void;

    /**
     * @return int<0, max>
     */
    public function count(): int;
}
