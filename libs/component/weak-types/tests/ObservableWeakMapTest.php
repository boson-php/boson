<?php

declare(strict_types=1);

namespace Boson\Component\WeakType\Tests;

use Boson\Component\WeakType\ObservableWeakMap;
use Boson\Tests\Unit\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/weak-types')]
final class ObservableWeakMapTest extends TestCase
{
    public function testCreateEmpty(): void
    {
        $map = new ObservableWeakMap();

        self::assertCount(0, $map);
    }

    public function testWatchAndFind(): void
    {
        $map = new ObservableWeakMap();
        $key = (object) [];
        $value = (object) [];

        $map->watch($key, $value, function () {});

        self::assertCount(1, $map);
        self::assertSame($value, $map->find($key));
    }

    public function testWatchWithCallback(): void
    {
        $map = new ObservableWeakMap();
        $key = (object) [];
        $value = (object) [];
        $callbackCalled = false;

        $map->watch($key, $value, function ($ref) use (&$callbackCalled, $value) {
            self::assertSame($value, $ref);
            $callbackCalled = true;
        });

        unset($key);
        \gc_collect_cycles();

        self::assertTrue($callbackCalled);
    }

    public function testFindNonExistent(): void
    {
        $map = new ObservableWeakMap();
        $key = (object) [];

        self::assertNull($map->find($key));
    }

    public function testIterator(): void
    {
        $map = new ObservableWeakMap();
        $key1 = (object) [];
        $value1 = (object) [];
        $key2 = (object) [];
        $value2 = (object) [];

        $map->watch($key1, $value1, function () {});
        $map->watch($key2, $value2, function () {});

        $items = [];
        foreach ($map as $key => $value) {
            $items[] = [$key, $value];
        }

        self::assertCount(2, $items);
        self::assertSame($value1, $items[0][1]);
        self::assertSame($value2, $items[1][1]);
    }

    public function testIteratorAfterKeyRemoval(): void
    {
        $map = new ObservableWeakMap();
        $key1 = (object) [];
        $value1 = (object) [];
        $key2 = (object) [];
        $value2 = (object) [];

        $map->watch($key1, $value1, function () {});
        $map->watch($key2, $value2, function () {});

        unset($key1);
        \gc_collect_cycles();

        $items = [];
        foreach ($map as $key => $value) {
            $items[] = [$key, $value];
        }

        self::assertCount(1, $items);
        self::assertSame($value2, $items[0][1]);
    }

    public function testMultipleValuesForSameKey(): void
    {
        $map = new ObservableWeakMap();
        $key = (object) [];
        $value1 = (object) [];
        $value2 = (object) [];

        $map->watch($key, $value1, function () {});
        $map->watch($key, $value2, function () {});

        self::assertCount(1, $map);
        self::assertSame($value2, $map->find($key));
    }

    public function testWatchReturnsKey(): void
    {
        $map = new ObservableWeakMap();
        $key = (object) [];
        $value = (object) [];

        $returnedKey = $map->watch($key, $value, function () {});

        self::assertSame($key, $returnedKey);
    }
}
