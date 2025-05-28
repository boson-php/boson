<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Family\Factory;

use Boson\Component\OsInfo\FamilyInterface;

final readonly class DefaultFamilyFactory implements FamilyFactoryInterface
{
    private FamilyFactoryInterface $default;

    public function __construct()
    {
        $this->default = EnvFamilyFactory::createForOverrideEnvVariables(
            delegate: new GenericFamilyFactory(),
        );
    }

    public function createFamily(): FamilyInterface
    {
        return $this->default->createFamily();
    }
}
