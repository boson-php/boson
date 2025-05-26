<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo;

use Boson\Component\OsInfo\Factory\InMemoryOperatingSystemFactory;
use Boson\Component\OsInfo\Factory\OperatingSystemFactory;

final readonly class OperatingSystem implements \Stringable
{
    /**
     * Gets the list of standards supported by this operating system.
     *
     * @var list<StandardInterface>
     */
    public array $standards;

    /**
     * @param iterable<mixed, StandardInterface> $standards
     */
    public function __construct(
        /**
         * Gets the name of the operating system.
         *
         * The name should be a non-empty string that uniquely identifies this
         * operating system. For example, "Ubuntu 22.04 LTS" or "Windows 11".
         *
         * @var non-empty-string
         */
        public string $name,
        /**
         * Gets the version of the operating system.
         *
         * @var non-empty-string
         */
        public string $version,
        /**
         * Gets the family this operating system belongs to.
         */
        public FamilyInterface $family,
        /**
         * Gets the codename of the operating system.
         *
         * @var non-empty-string|null
         */
        public ?string $codename = null,
        /**
         * Gets the edition of the operating system.
         *
         * @var non-empty-string|null
         */
        public ?string $edition = null,
        iterable $standards = [],
    ) {
        $this->standards = \iterator_to_array($standards, false);
    }

    /**
     * @api
     */
    public static function createFromGlobals(): OperatingSystem
    {
        static $factory = new InMemoryOperatingSystemFactory(
            delegate: new OperatingSystemFactory(),
        );

        return $factory->createOperatingSystem();
    }

    /**
     * Checks if this operating system supports the given standard.
     *
     * This method checks if any of the standards supported by this operating
     * system (including standards of its family) supports the given standard.
     *
     * @api
     */
    public function isSupports(StandardInterface $standard): bool
    {
        foreach ($this->standards as $actual) {
            if ($actual->isSupports($standard)) {
                return true;
            }
        }

        return false;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
