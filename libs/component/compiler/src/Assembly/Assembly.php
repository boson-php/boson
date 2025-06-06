<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Assembly;

use Boson\Component\Compiler\Configuration;

final readonly class Assembly implements \Stringable
{
    public function __construct(
        public AssemblyPlatform $platform,
        public AssemblyArchitecture $arch,
        public AssemblyEdition $edition,
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
            . \DIRECTORY_SEPARATOR . \strtolower($this->platform->name)
            . \DIRECTORY_SEPARATOR . \strtolower($this->arch->name);
    }

    public function __toString(): string
    {
        return \strtolower($this->platform->name)
            . '/' . \strtolower($this->arch->name);
    }
}
