<?php

declare(strict_types=1);

namespace Boson\Component\Ipc;

use Boson\Component\Ipc\Driver\DriverFactory;
use Boson\Component\Ipc\Driver\DriverInterface;
use Boson\Component\Ipc\Exception\DriverException;
use Boson\Component\Ipc\Exception\ProtocolException;
use Boson\Component\Ipc\Exception\ProtocolHeaderMagikException;
use Boson\Component\Ipc\Mutex\SpinlockMemoryMutex;
use Boson\Component\Ipc\Mutex\MutexInterface;
use Boson\Component\Ipc\Protocol\Struct\MemoryHeader;

final class Channel
{
    /**
     * Contains default driver instance
     */
    private static DriverInterface $default;

    private readonly MemoryHeader $header;

    private readonly MutexInterface $mutex;

    /**
     * @param non-empty-string $name
     */
    public function __construct(
        /**
         * @var non-empty-string
         */
        public readonly string $name,
        private readonly DriverInterface $driver,
    ) {
        $this->mutex = new SpinlockMemoryMutex(
            driver: $this->driver,
            offset: MemoryHeader::BOSON_HEADER_SIZE,
        );

        $this->header = $this->loadHeader();
    }

    private function loadHeader(): MemoryHeader
    {
        $this->mutex->acquire();

        try {
            $header = MemoryHeader::fromBytes(
                data: $this->driver->read(
                    size: MemoryHeader::BOSON_HEADER_SIZE,
                ),
            );

            if ($header->isEmpty) {
                // Init default header
                $header = $this->writeDefaultHeader();
            }

            if (!$header->isValid) {
                throw ProtocolHeaderMagikException::becauseInvalidMagik();
            }

            if ($header->version !== MemoryHeader::BOSON_IPC_VERSION) {
                $this->driver->clear();

                // TODO need to revalidate your recorded header?
                // Rebuild memory
                $header = $this->writeDefaultHeader();
            }
        } finally {
            $this->mutex->release();
        }

        return $header;
    }

    private function writeDefaultHeader(): MemoryHeader
    {
        $header = MemoryHeader::createDefault();

        $this->driver->write((string) $header);

        return $header;
    }

    /**
     * @param non-empty-string $name
     * @throws DriverException in case of driver error occurs
     * @throws ProtocolException in case of invalid memory signature
     */
    public static function create(string $name): self
    {
        assert($name !== '');

        self::$default ??= DriverFactory::createFromDefaultFactory();

        return new self($name, self::$default);
    }
}
