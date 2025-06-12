<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Configuration;

final readonly class FinderIncludeConfiguration extends IncludeConfiguration
{
    public function __construct(
        /**
         * @var non-empty-list<non-empty-string>
         */
        public array $directories,
        /**
         * @var list<non-empty-string>
         */
        public array $notDirectories = [],
        /**
         * @var list<non-empty-string>
         */
        public array $names = [],
        /**
         * @var list<non-empty-string>
         */
        public array $notNames = [],
    ) {}
}
