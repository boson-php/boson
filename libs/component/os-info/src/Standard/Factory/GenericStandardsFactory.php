<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Standard\Factory;

use Boson\Component\OsInfo\Family;
use Boson\Component\OsInfo\FamilyInterface;
use Boson\Component\OsInfo\Standard;

final class GenericStandardsFactory implements StandardsFactoryInterface
{
    public function createStandards(FamilyInterface $family): array
    {
        if ($family->is(Family::Unix)) {
            return [Standard::Posix];
        }

        return [];
    }
}
