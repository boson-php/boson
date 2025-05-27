<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Family\Factory;

use Boson\Component\OsInfo\FamilyInterface;

interface FamilyFactoryInterface
{
    public function createFamily(): FamilyInterface;
}
