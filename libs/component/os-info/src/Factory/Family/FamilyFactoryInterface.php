<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Family;

use Boson\Component\OsInfo\FamilyInterface;

interface FamilyFactoryInterface
{
    public function createFamily(): FamilyInterface;
}
