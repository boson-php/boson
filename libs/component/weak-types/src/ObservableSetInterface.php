<?php

declare(strict_types=1);

namespace Boson\Component\WeakType;

/**
 * Allows to store a set of objects and track their
 * destruction (react to GC cleanup).
 *
 *  ```
 *  $set->watch($object, function (ExampleObject $ref) {
 *       echo vsprintf('ExampleObject(%d) has been destroyed', [
 *           get_object_id($ref),
 *       ]);
 *  ));
 *  ```
 *
 * @template TObservable of object = object
 *
 * @template-extends \Traversable<array-key, TObservable>
 */
interface ObservableSetInterface extends \Countable, \Traversable
{
    /**
     * @param TObservable $entry
     * @param \Closure(TObservable):void $onRelease
     *
     * @return TObservable
     */
    public function watch(object $entry, \Closure $onRelease): object;

    /**
     * @param TObservable $entry
     */
    public function detach(object $entry): void;

    /**
     * @return int<0, max>
     */
    public function count(): int;
}