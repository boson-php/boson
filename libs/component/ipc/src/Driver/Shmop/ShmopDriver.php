<?php

declare(strict_types=1);

namespace Boson\Component\Ipc\Driver\Shmop;

use Boson\Component\Ipc\Driver\DriverInterface;
use Boson\Component\Ipc\Exception\DriverMemoryException;
use Boson\Component\Ipc\Exception\DriverReadException;
use Boson\Component\Ipc\Exception\DriverWriteException;

final class ShmopDriver implements DriverInterface
{
    private readonly \Shmop $connection;

    public int $size {
        get => \shmop_size($this->connection);
    }

    /**
     * @throws DriverMemoryException
     */
    public function __construct(ShmopDriverCreateInfo $config)
    {
        $this->connection = $this->connect(
            id: $config->memoryId,
            size: $config->memoryInitialSize,
        );
    }

    public function write(string $data, int $offset = 0): void
    {
        assert($offset >= 0);

        $bytes = \strlen($data);

        if ($bytes === 0) {
            return;
        }

        // Determine that the memory allocation is required
        if ($bytes + $offset > ($currentAllowedMemory = $this->size)) {
            throw DriverMemoryException::becauseAllocationFailed(
                current: $currentAllowedMemory,
                expected: $bytes + $offset,
            );
        }

        $bytesWritten = \shmop_write($this->connection, $data, $offset);

        if ($bytes !== $bytesWritten) {
            throw DriverWriteException::becauseWriteFailed($bytes, $offset);
        }
    }

    public function read(int $size, int $offset = 0): string
    {
        assert($offset >= 0);

        if ($size <= 0) {
            return '';
        }

        try {
            $buffer = \shmop_read($this->connection, $offset, $size);
        } catch (\ValueError $e) {
            throw DriverReadException::becauseReadFailed($size, $offset, $e);
        }

        return $buffer;
    }

    /**
     * @throws DriverMemoryException
     */
    private function connect(int $id, int $size): \Shmop
    {
        // First, we try to connect to the memory to Read + Write
        $result = @\shmop_open($id, 'c', 0o644, $size);

        if ($result === false) {
            throw DriverMemoryException::becauseAllocationFailed(
                current: 0,
                expected: $size,
            );
        }

        return $result;
    }

    public function clear(): void
    {
        \shmop_delete($this->connection);
    }
}
