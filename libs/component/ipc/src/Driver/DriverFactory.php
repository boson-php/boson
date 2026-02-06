<?php

declare(strict_types=1);

namespace Boson\Component\Ipc\Driver;

use Boson\Component\Ipc\Driver\Shmop\ShmopDriverFactory;
use Boson\Component\Ipc\Exception\DriverNotAvailableException;

final class DriverFactory implements DriverFactoryInterface
{
    /**
     * Default driver factory instance
     */
    private static self $default;

    /**
     * Contains a list of ALL supported drivers
     *
     * @var list<DriverFactoryInterface>
     */
    private iterable $drivers {
        get {
            if (!\is_array($this->drivers)) {
                return $this->drivers = \iterator_to_array($this->drivers, false);
            }

            return $this->drivers;
        }
    }

    /**
     * Contains current driver instance
     */
    private ?DriverFactoryInterface $current = null {
        get => $this->current ??= $this->select();
    }

    public bool $isSupported {
        get => $this->current !== null;
    }

    /**
     * @param iterable<mixed, DriverFactoryInterface> $drivers
     */
    public function __construct(iterable $drivers = [])
    {
        $this->drivers = $drivers;
    }

    /**
     * @throws DriverNotAvailableException
     */
    public static function createFromDefaultFactory(): DriverInterface
    {
        return self::default()
            ->create();
    }

    /**
     * Returns default driver factory instance
     */
    public static function default(): self
    {
        return self::$default ??= new self([
            new ShmopDriverFactory(),
        ]);
    }

    /**
     * Selects the most suitable and available driver factory
     */
    private function select(): ?DriverFactoryInterface
    {
        foreach ($this->drivers as $driver) {
            if (!$driver->isSupported) {
                continue;
            }

            return $driver;
        }

        return null;
    }

    public function create(): DriverInterface
    {
        $current = $this->current;

        if ($current === null) {
            throw DriverNotAvailableException::becauseNoSuitableDriver();
        }

        return $current->create();
    }
}
