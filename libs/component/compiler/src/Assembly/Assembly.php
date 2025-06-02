<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Assembly;

use Boson\Component\Compiler\Assembly\EditionInterface as PhpEditionInterface;
use Boson\Component\Compiler\Configuration;
use Boson\Component\CpuInfo\ArchitectureInterface as CpuArchitectureInterface;
use Boson\Component\OsInfo\FamilyInterface as OsFamilyInterface;

final readonly class Assembly implements \Stringable
{
    public function __construct(
        public OsFamilyInterface $family,
        public CpuArchitectureInterface $arch,
        public PhpEditionInterface $edition,
        /**
         * @var non-empty-string
         */
        public string $frontend,
        /**
         * @var non-empty-string
         */
        public string $backend,
    ) {}

    /**
     * @return non-empty-string
     */
    public function getBuildDirectory(Configuration $config): string
    {
        return $config->output
            . \DIRECTORY_SEPARATOR . \strtolower($this->family->name)
            . \DIRECTORY_SEPARATOR . \strtolower($this->arch->name);
    }

    public function __toString(): string
    {
        return \strtolower($this->family->name)
            . '/' . \strtolower($this->arch->name);
    }
}
