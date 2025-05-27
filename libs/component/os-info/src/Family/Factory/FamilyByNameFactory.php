<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Family\Factory;

use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\FamilyInterface;

abstract readonly class FamilyByNameFactory implements FamilyFactoryInterface
{
    /**
     * @param non-empty-string $name
     */
    protected function createFromName(string $name): FamilyInterface
    {
        return match (\strtolower($name)) {
            'windows' => Family::Windows,
            'linux' => Family::Linux,
            'bsd' => Family::BSD,
            'darwin' => Family::Darwin,
            'solaris' => Family::Solaris,
            default => new Family($name),
        };
    }
}
