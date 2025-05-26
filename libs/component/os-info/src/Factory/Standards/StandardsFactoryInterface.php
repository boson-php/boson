<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Standards;

use Boson\Component\OsInfo\FamilyInterface;
use Boson\Component\OsInfo\StandardInterface;

interface StandardsFactoryInterface
{
    /**
     * @return list<StandardInterface>
     */
    public function createStandards(FamilyInterface $family): array;
}
