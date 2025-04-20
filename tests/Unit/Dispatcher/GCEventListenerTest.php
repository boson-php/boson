<?php

declare(strict_types=1);

namespace Boson\Tests\Unit\Dispatcher;

use Boson\Dispatcher\DelegateEventListener;
use Boson\Dispatcher\EventListener;
use PHPUnit\Framework\Attributes\Group;
use Boson\Tests\Unit\Dispatcher\Stub\EventListenerContainerStub;

#[Group('dispatcher')]
final class GCEventListenerTest extends DispatcherTestCase
{
    public function testMemoryFreedAfterDisposeDispatcher(): void
    {
        [$container, $reference] = $this->getEventListenerContainerStub(new EventListener());

        // Reference must not be empty when container is defined
        self::assertNotNull($reference->get());

        // Trigger listener
        $container->dispatch($this);

        // Remove container
        unset($container);

        // Listener must be removed
        self::assertNull($reference->get());
    }

    public function testDelegateMemoryFreedAfterDisposeDispatcher(): void
    {
        // Parent event listener
        $parent = new EventListener();

        [$container, $reference] = $this->getEventListenerContainerStub(
            listener: new DelegateEventListener($parent),
        );

        // Reference must not be empty when container is defined
        self::assertNotNull($reference->get());

        // Trigger listener
        $container->dispatch($this);

        // Remove container
        unset($container);

        // Listener must be removed
        self::assertNull($reference->get());

        // Keep reference to parent event listener
        self::assertNotNull($parent);
    }

    /**
     * Using an external function/method is required to remove
     * all internal references to the listener and start the GC process.
     */
    private function getEventListenerContainerStub(EventListener $listener): array
    {
        $callback = function (object $e): void {
            // Keep reference to "this" to check that the additional
            // internal reference does not affect the GC cleanup process.
            if ($e === $this) {
                // do nothing
            }
        };

        $reference = \WeakReference::create($callback);

        $container = new EventListenerContainerStub($listener);
        $container->addEventListener(self::class, $callback);

        return [$container, $reference];
    }
}
