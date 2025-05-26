<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory;

use Boson\Component\OsInfo\OperatingSystem;

final class InMemoryOperatingSystemFactory implements OperatingSystemFactoryInterface
{
    private ?OperatingSystem $current = null;

    public function __construct(
        private readonly OperatingSystemFactoryInterface $delegate,
    ) {}

    public function createOperatingSystem(): OperatingSystem
    {
        return $this->current ??= $this->delegate->createOperatingSystem();
    }
}
