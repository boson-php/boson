<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Standard\Factory;

use Boson\Component\OsInfo\FamilyInterface;

final readonly class DefaultStandardsFactory implements StandardsFactoryInterface
{
    private StandardsFactoryInterface $default;

    public function __construct()
    {
        $this->default = new GenericStandardsFactory();
    }

    public function createStandards(FamilyInterface $family): array
    {
        return $this->default->createStandards($family);
    }
}
