<?php

declare(strict_types=1);

namespace Boson\Component\WeakType\Tests;

use Boson\Component\WeakType\Tests\WeakClosureTest\ParentStub;
use Boson\Component\WeakType\Tests\WeakClosureTest\StaticStub;
use Boson\Component\WeakType\WeakClosure;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('boson-php/weak-types')]
final class WeakClosureTest extends TestCase
{
    public function testCreateReturnsSameInstance(): void
    {
        $closure = WeakClosure::create(static function () {
            return 123;
        });

        self::assertSame($closure, WeakClosure::create($closure));
    }

    public function testInstanceMethodWhileObjectAlive(): void
    {
        $object = new class {
            public function foo(): int
            {
                return 42;
            }
        };

        $closure = WeakClosure::create($object->foo(...));

        self::assertSame(42, $closure());
    }

    public function testInstanceMethodAfterGarbageCollectionThrows(): void
    {
        $closure = (function () {
            $object = new class {
                public function foo(): int
                {
                    return 42;
                }
            };

            return WeakClosure::create($object->foo(...));
        })();

        gc_collect_cycles();

        $this->expectException(\RuntimeException::class);
        $closure();
    }

    public function testProtectedMethodScopeIsPreserved(): void
    {
        $object = new class {
            protected function foo(): int
            {
                return 123;
            }

            public function get(): \Closure
            {
                return $this->foo(...);
            }
        };

        $closure = WeakClosure::create($object->get());

        self::assertSame(123, $closure());
    }

    public function testInheritedMethodAfterGarbageCollection(): void
    {
        $closure = (function () {
            $object = new class extends ParentStub {
                public function get(): \Closure
                {
                    return $this->value(...);
                }
            };

            return WeakClosure::create($object->get());
        })();

        gc_collect_cycles();

        $this->expectException(\RuntimeException::class);
        $closure();
    }

    public function testStaticMethodIsCallableAfterGc(): void
    {
        $closure = WeakClosure::create(StaticStub::value(...));

        gc_collect_cycles();

        self::assertSame(10, $closure());
    }

    public function testPureClosureIsNotWeakReferenced(): void
    {
        $closure = WeakClosure::create(fn () => 123);

        gc_collect_cycles();

        self::assertSame(123, $closure());
    }

    public function testRepeatedInvocationAfterGcAlwaysFails(): void
    {
        $closure = (function () {
            $object = new class {
                public function foo(): int { return 1; }
            };
            return WeakClosure::create($object->foo(...));
        })();

        gc_collect_cycles();

        try {
            $closure();
        } catch (\RuntimeException) {}

        $this->expectException(\RuntimeException::class);
        $closure();
    }

    public function testInvokeMethod(): void
    {
        $object = new class {
            public function __invoke(): int
            {
                return 7;
            }
        };

        $closure = WeakClosure::create($object(...));

        self::assertSame(7, $closure());
    }
}
