<?php

declare(strict_types=1);

namespace Boson\Component\Ipc\Driver;

use Boson\Component\Ipc\Exception\DriverMemoryException;
use Boson\Component\Ipc\Exception\DriverReadException;
use Boson\Component\Ipc\Exception\DriverWriteException;

interface DriverInterface
{
    /**
     * Gets the amount of allocated memory.
     *
     * @var int<0, max>
     */
    public int $size {
        get;
    }

    /**
     * @param int<0, max> $offset
     *
     * @throws DriverWriteException in case the driver cannot write data
     * @throws DriverMemoryException in case the driver cannot allocate enough memory
     */
    public function write(string $data, int $offset = 0): void;

    /**
     * @param int<1, max> $size
     * @param int<0, max> $offset
     * @return non-empty-string
     *
     * @throws DriverReadException in case the driver cannot read the data
     */
    public function read(int $size, int $offset = 0): string;

    public function clear(): void;
}
