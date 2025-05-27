<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Family\Factory;

use Boson\Component\OsInfo\FamilyInterface;

final class InMemoryFamilyFactory implements FamilyFactoryInterface
{
    private ?FamilyInterface $family = null;

    public function __construct(
        private readonly FamilyFactoryInterface $delegate,
    ) {}

    public function createFamily(): FamilyInterface
    {
        return $this->family ??= $this->delegate->createFamily();
    }
}
