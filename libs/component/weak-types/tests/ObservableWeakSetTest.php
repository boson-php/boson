<?php

declare(strict_types=1);

namespace Boson\Component\WeakType\Tests;

use Boson\Component\WeakType\ObservableWeakSet;
use Boson\Tests\Unit\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/weak-types')]
final class ObservableWeakSetTest extends TestCase
{
    public function testCreateEmpty(): void
    {
        $set = new ObservableWeakSet();

        self::assertCount(0, $set);
    }

    public function testWatch(): void
    {
        $set = new ObservableWeakSet();
        $object = (object) [];

        $set->watch($object, function () {});

        self::assertCount(1, $set);
    }

    public function testWatchWithCallback(): void
    {
        $set = new ObservableWeakSet();
        $object = (object) [];
        $callbackCalled = false;

        $set->watch($object, function ($ref) use (&$callbackCalled, $object) {
            self::assertSame($object, $ref);
            $callbackCalled = true;
        });

        unset($object);
        \gc_collect_cycles();

        self::assertTrue($callbackCalled);
    }

    public function testIterator(): void
    {
        $set = new ObservableWeakSet();
        $object1 = (object) [];
        $object2 = (object) [];

        $set->watch($object1, function () {});
        $set->watch($object2, function () {});

        $items = [];
        foreach ($set as $object) {
            $items[] = $object;
        }

        self::assertCount(2, $items);
        self::assertSame($object1, $items[0]);
        self::assertSame($object2, $items[1]);
    }

    public function testIteratorAfterObjectRemoval(): void
    {
        $set = new ObservableWeakSet();
        $object1 = (object) [];
        $object2 = (object) [];

        $set->watch($object1, function () {});
        $set->watch($object2, function () {});

        unset($object1);
        \gc_collect_cycles();

        $items = [];
        foreach ($set as $object) {
            $items[] = $object;
        }

        self::assertCount(1, $items);
        self::assertSame($object2, $items[0]);
    }

    public function testWatchReturnsObject(): void
    {
        $set = new ObservableWeakSet();
        $object = (object) [];

        $returnedObject = $set->watch($object, function () {});

        self::assertSame($object, $returnedObject);
    }

    public function testMultipleWatchSameObject(): void
    {
        $set = new ObservableWeakSet();
        $object = (object) [];
        $callback1Called = false;
        $callback2Called = false;

        $set->watch($object, function () use (&$callback1Called) {
            $callback1Called = true;
        });
        $set->watch($object, function () use (&$callback2Called) {
            $callback2Called = true;
        });

        unset($object);
        \gc_collect_cycles();

        self::assertTrue($callback1Called);
        self::assertTrue($callback2Called);
    }
} 