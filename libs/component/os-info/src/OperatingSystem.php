<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo;

use Boson\Component\OsInfo\Factory\DefaultOperatingSystemFactory;
use Boson\Component\OsInfo\Factory\InMemoryOperatingSystemFactory;
use Boson\Component\OsInfo\Vendor\VendorInfo;

final readonly class OperatingSystem extends VendorInfo
{
    /**
     * Gets the list of standards supported by this operating system.
     *
     * @var list<StandardInterface>
     */
    public array $standards;

    /**
     * @param non-empty-string $name
     * @param non-empty-string $version
     * @param non-empty-string|null $codename
     * @param non-empty-string|null $edition
     * @param iterable<mixed, StandardInterface> $standards
     */
    public function __construct(
        /**
         * Gets the family this operating system belongs to.
         */
        public FamilyInterface $family,
        string $name,
        string $version,
        ?string $codename = null,
        ?string $edition = null,
        iterable $standards = [],
    ) {
        $this->standards = \iterator_to_array($standards, false);

        parent::__construct(
            name: $name,
            version: $version,
            codename: $codename,
            edition: $edition,
        );
    }

    /**
     * @api
     */
    public static function createFromGlobals(): OperatingSystem
    {
        static $factory = new InMemoryOperatingSystemFactory(
            delegate: new DefaultOperatingSystemFactory(),
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
