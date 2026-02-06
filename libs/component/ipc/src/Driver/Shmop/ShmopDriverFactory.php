<?php

declare(strict_types=1);

namespace Boson\Component\Ipc\Driver\Shmop;

use Boson\Component\Ipc\Driver\DriverFactoryInterface;
use Boson\Component\Ipc\Driver\DriverInterface;
use Boson\Component\Ipc\Exception\DriverNotAvailableException;

final class ShmopDriverFactory implements DriverFactoryInterface
{
    public const int DEFAULT_MEMORY_SIZE = 1024 * 64;

    public bool $isSupported {
        get => $this->isSupported ??= \extension_loaded('shmop');
    }

    /**
     * @var int<-1, max>
     */
    private int $memoryId {
        get => $this->memoryId ??= \ftok(__FILE__, 'b');
    }

    /**
     * @param int<-1, max>|null $memoryId
     */
    public function __construct(
        ?int $memoryId = null,
        /**
         * @var int<0, max>
         */
        private readonly int $memoryInitialSize = self::DEFAULT_MEMORY_SIZE,
    ) {
        if ($memoryId !== null) {
            $this->memoryId = $memoryId;
        }
    }

    public function create(): DriverInterface
    {
        if (!$this->isSupported) {
            throw DriverNotAvailableException::becauseExtensionRequired('shmop');
        }

        return new ShmopDriver(
            config: new ShmopDriverCreateInfo(
                memoryId: $this->memoryId,
                memoryInitialSize: $this->memoryInitialSize,
            ),
        );
    }
}
