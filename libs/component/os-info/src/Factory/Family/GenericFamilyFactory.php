<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Family;

use Boson\Component\OsInfo\FamilyInterface;

final readonly class GenericFamilyFactory extends FamilyByNameFactory
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        private string $name = \PHP_OS_FAMILY,
    ) {}

    public function createFamily(): FamilyInterface
    {
        return $this->createFromName($this->name);
    }
}
