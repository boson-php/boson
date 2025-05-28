<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory;

use Boson\Component\OsInfo\OperatingSystem;

final readonly class DefaultOperatingSystemFactory implements OperatingSystemFactoryInterface
{
    private OperatingSystemFactoryInterface $default;

    public function __construct()
    {
        $this->default = new GenericOperatingSystemFactory();
    }

    public function createOperatingSystem(): OperatingSystem
    {
        return $this->default->createOperatingSystem();
    }
}
