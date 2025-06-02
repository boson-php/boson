<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Assembly;

use Boson\Component\Compiler\Configuration;
use Boson\Component\CpuInfo\ArchitectureInterface as CpuArchitectureInterface;
use Boson\Component\OsInfo\FamilyInterface as OsFamilyInterface;
use Boson\Component\Compiler\Assembly\EditionInterface as PhpEditionInterface;

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
        public string $extension = '',
    ) {}

    /**
     * @return non-empty-string
     */
    public function getBuildDirectory(Configuration $config): string
    {
        return $config->build
            . \DIRECTORY_SEPARATOR . $this->family
            . \DIRECTORY_SEPARATOR . $this->arch;
    }

    /**
     * @return non-empty-string
     */
    public function getBuildBinaryPathname(Configuration $config): string
    {
        $pathname = $this->getBuildDirectory($config)
            . \DIRECTORY_SEPARATOR . $config->name;

        if ($this->extension !== '') {
            $pathname .= '.' . $this->extension;
        }

        return $pathname;
    }

    public function __toString(): string
    {
        return $this->family . '/' . $this->arch;
    }
}
