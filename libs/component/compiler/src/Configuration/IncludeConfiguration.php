<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Configuration;

final readonly class IncludeConfiguration
{
    public function __construct(
        /**
         * @var non-empty-string|null
         */
        public ?string $directory,
        /**
         * @var non-empty-string|null
         */
        public ?string $name,
    ) {}
}
