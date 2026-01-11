<?php

declare(strict_types=1);

namespace Boson\Component\WeakType\Tests;

use Boson\Component\WeakType\ObservableSet;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/weak-types')]
final class ObservableSetTest extends TestCase
{
    public function testCreateEmpty(): void
    {
        $set = new ObservableSet();

        self::assertCount(0, $set);
    }

    public function testWatch(): void
    {
        $set = new ObservableSet();
        $object = (object) [];

        $set->watch($object, function () {});

        self::assertCount(1, $set);
    }

    public function testWatchWithCallback(): void
    {
        $set = new ObservableSet();
        $object = (object) [];
        $callbackCalled = false;

        $set->watch($object, function ($ref) use (&$callbackCalled, $object) {
            self::assertSame($object, $ref);
            $callbackCalled = true;
        });

        unset($object);
        \gc_collect_cycles();

        self::assertFalse($callbackCalled);
    }

    public function testDetachableWithCallback(): void
    {
        $set = new ObservableSet();
        $object = (object) [];
        $callbackCalled = false;

        $set->watch($object, function ($ref) use (&$callbackCalled, $object) {
            self::assertSame($object, $ref);
            $callbackCalled = true;
        });

        $set->detach($object);

        self::assertTrue($callbackCalled);
    }

    public function testIterator(): void
    {
        $set = new ObservableSet();
        $object1 = (object) [];
        $object2 = (object) [];

        $set->watch($object1, function () {});
        $set->watch($object2, function () {});

        $items = \iterator_to_array($set, false);

        self::assertCount(2, $items);
        self::assertSame($object1, $items[0]);
        self::assertSame($object2, $items[1]);
    }

    public function testIteratorAfterObjectRemoval(): void
    {
        $set = new ObservableSet();
        $object1 = (object) [];
        $object2 = (object) [];

        $set->watch($object1, function () {});
        $set->watch($object2, function () {});

        unset($object1);
        unset($object2);
        \gc_collect_cycles();

        $items = \iterator_to_array($set, false);

        self::assertCount(2, $items);
        self::assertNotNull($items[0]);
        self::assertNotNull($items[1]);


        $set->detach($items[0]);
        $items = \iterator_to_array($set, false);

        self::assertCount(1, $items);
        self::assertNotNull($items[0]);
    }

    public function testWatchReturnsObject(): void
    {
        $set = new ObservableSet();
        $object = (object) [];

        $returnedObject = $set->watch($object, function () {});

        self::assertSame($object, $returnedObject);
    }

    public function testMultipleWatchSameObject(): void
    {
        $set = new ObservableSet();
        $object = (object) [];
        $callback1Called = false;
        $callback2Called = false;

        $set->watch($object, function () use (&$callback1Called) {
            $callback1Called = true;
        });

        $set->watch($object, function () use (&$callback2Called) {
            $callback2Called = true;
        });

        $set->detach($object);

        self::assertTrue($callback1Called);
        self::assertTrue($callback2Called);
    }
}
