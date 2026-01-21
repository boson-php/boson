<?php

declare(strict_types=1);

namespace Boson\Api\OperatingSystem;

use Boson\Component\OsInfo\FamilyInterface;
use Boson\Component\OsInfo\OperatingSystem;
use Boson\Component\OsInfo\StandardInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Api\OperatingSystem
 */
final class OperatingSystemInfo implements OperatingSystemInfoInterface
{
    private OperatingSystem $os {
        get => $this->os ??= OperatingSystem::createFromGlobals();
    }

    public string $name {
        get => $this->os->name;
    }

    public string $version {
        get => $this->os->version;
    }

    public ?string $codename {
        get => $this->os->codename;
    }

    public ?string $edition {
        get => $this->os->edition;
    }

    public FamilyInterface $family {
        get => $this->os->family;
    }

    public iterable $standards {
        get => $this->os->standards;
    }

    public function isSupports(StandardInterface $standard): bool
    {
        return $this->os->isSupports($standard);
    }

    public function __debugInfo(): array
    {
        return [
            'name' => $this->name,
            'version' => $this->version,
            'edition' => $this->edition,
            'family' => $this->family,
            'standards' => $this->standards,
        ];
    }
}
