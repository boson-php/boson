<?php

declare(strict_types=1);

namespace Boson\Component\Ipc\Mutex;

use Boson\Component\Ipc\Driver\DriverInterface;

/**
 * Optimistic spinlock without an atomic primitive
 */
final class SpinlockMemoryMutex implements MutexInterface
{
    public const int LOCK_SIZE = 4;
    public const string FREE_BYTES = "\0\0\0\0";

    public readonly int $pid;

    private readonly string $pidBytes;

    public private(set) bool $isAcquired = false;

    public function __construct(
        private readonly DriverInterface $driver,
        /**
         * @var int<0, max>
         */
        private readonly int $offset,
    ) {
        $this->pid = (int) \getmypid();
        $this->pidBytes = \pack('V', $this->pid);
    }

    public function acquire(int $attempts = 100): void
    {
        if ($this->isAcquired) {
            return;
        }

        for ($attempt = 0; $attempt < $attempts; ++$attempt) {
            // 1. Trying to capture atomically (Test-And-Set emulation)
            $ownedPidBytes = $this->driver->read(self::LOCK_SIZE, $this->offset);

            // Wait (is not free and not ours)
            if ($ownedPidBytes !== self::FREE_BYTES && $ownedPidBytes !== $this->pidBytes) {
                \usleep(100 * $attempt);

                continue;
            }

            // 2. Trying to set our PID
            $this->driver->write($this->pidBytes, $this->offset);

            // 3. Read back immediately
            if ($this->driver->read(self::LOCK_SIZE, $this->offset) === $this->pidBytes) {
                $this->isAcquired = true;

                return;
            }
        }

        throw new \RuntimeException("Failed to acquire mutex after $attempts attempts");
    }

    public function release(): void
    {
        if (!$this->isAcquired) {
            return;
        }

        $this->isAcquired = false;
        $this->driver->write(self::FREE_BYTES, $this->offset);
    }

    public function __destruct()
    {
        $this->release();
    }
}
