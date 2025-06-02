<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Configuration;

final readonly class DirectoryIncludeConfiguration extends IncludeConfiguration
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public string $directory,
    ) {}
}
