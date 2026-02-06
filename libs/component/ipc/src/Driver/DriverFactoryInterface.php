<?php

declare(strict_types=1);

namespace Boson\Component\Ipc\Driver;

use Boson\Component\Ipc\Exception\DriverMemoryException;
use Boson\Component\Ipc\Exception\DriverNotAvailableException;

interface DriverFactoryInterface
{
    /**
     * Indicates whether the driver is supported.
     */
    public bool $isSupported {
        get;
    }

    /**
     * Creates a new instance of the driver.
     *
     * @throws DriverNotAvailableException in case the driver is not creatable
     * @throws DriverMemoryException in case the driver cannot allocate enough memory
     */
    public function create(): DriverInterface;
}
